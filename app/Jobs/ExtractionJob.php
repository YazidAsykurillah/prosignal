<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\DiscoveryRun;
use App\Contracts\AIProviderInterface;
use App\Services\AI\Prompts\BatchedCompanyExtractionPrompt;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class ExtractionJob implements ShouldQueue
{
    use Batchable, Queueable;

    public function __construct(
        public Project $project,
        public DiscoveryRun $discoveryRun,
        public Collection $sources,
        public string $signal
    ) {}

    public function handle(AIProviderInterface $aiProvider): void
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        // Sleep to avoid rate limits
        sleep(4);

        $promptSources = $this->sources->map(function ($source) {
            return [
                'source_id' => $source->id,
                'url' => $source->url,
                'content' => $source->content,
            ];
        })->toArray();

        $prompt = BatchedCompanyExtractionPrompt::build($promptSources, $this->signal);
        $payload = ['prompt' => $prompt];
        
        $result = $aiProvider->generate($payload);
        $results = BatchedCompanyExtractionPrompt::parse($result['raw_response']);

        // Update the discovery run tracking with the AI provider used
        if ($this->discoveryRun->ai_provider !== $result['provider']) {
            $this->discoveryRun->update([
                'ai_provider' => $result['provider'],
                'ai_model' => $result['model'],
            ]);
        }

        $persistenceJobs = [];

        foreach ($results as $result) {
            $sourceId = $result['source_id'] ?? null;
            if (!$sourceId) {
                continue;
            }

            $source = $this->sources->firstWhere('id', $sourceId);
            if ($source) {
                $source->update(['analyzed' => true]);
                $this->discoveryRun->increment('urls_analyzed');
            }

            if (!empty($result['company_name'])) {
                $persistenceJobs[] = new PersistenceJob($this->project, $this->discoveryRun, $source, $result, $this->signal);
            }
        }

        if (count($persistenceJobs) > 0) {
            if ($this->batch()) {
                $this->batch()->add($persistenceJobs);
            } else {
                foreach ($persistenceJobs as $job) {
                    dispatch($job);
                }
            }
        }
    }
}
