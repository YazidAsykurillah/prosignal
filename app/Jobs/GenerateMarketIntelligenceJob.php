<?php

namespace App\Jobs;

use App\Contracts\AIProviderInterface;
use App\Models\Project;
use App\Models\AiGeneration;
use App\Models\MarketIntelligence;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateMarketIntelligenceJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 120;

    protected Project $project;
    protected int $generationId;

    /**
     * Create a new job instance.
     */
    public function __construct(Project $project, int $generationId)
    {
        $this->project = $project;
        $this->generationId = $generationId;
    }

    /**
     * Execute the job.
     */
    public function handle(AIProviderInterface $aiProvider): void
    {
        $generation = AiGeneration::find($this->generationId);
        if (!$generation) {
            return;
        }

        $generation->update(['status' => 'processing']);

        try {
            $promptUsed = null;
            $result = $aiProvider->generateMarketIntelligence($this->project, $promptUsed);

            $generation->update([
                'status' => 'completed',
                'prompt' => $promptUsed,
                'response' => $result['raw_response'] ?? null,
                // Tokens used can be added if the provider API returns them.
            ]);

            MarketIntelligence::updateOrCreate(
                ['project_id' => $this->project->id],
                [
                    'industries' => $result['industries'] ?? [],
                    'roles' => $result['roles'] ?? [],
                    'company_sizes' => $result['company_sizes'] ?? [],
                    'opportunity_signals' => $result['opportunity_signals'] ?? [],
                    'discovery_keywords' => $result['discovery_keywords'] ?? [],
                    'raw_ai_response' => $result['raw_response'] ?? null,
                ]
            );

            activity()
                ->performedOn($this->project)
                ->log('Market Intelligence Generated');

        } catch (\Exception $e) {
            $generation->update(['status' => 'failed']);
            Log::error("Failed to generate market intelligence for Project {$this->project->id}: " . $e->getMessage());

            activity()
                ->performedOn($this->project)
                ->log('AI Generation Failed');

            throw $e;
        }
    }
}
