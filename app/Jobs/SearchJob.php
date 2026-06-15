<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\DiscoveryRun;
use App\Models\DiscoverySource;
use App\Services\Firecrawl\FirecrawlService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SearchJob implements ShouldQueue
{
    use Batchable, Queueable;

    public function __construct(
        public Project $project,
        public DiscoveryRun $discoveryRun,
        public string $keyword,
        public string $signal
    ) {}

    public function handle(FirecrawlService $firecrawlService): void
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        $searchQuery = $this->project->location 
            ? "{$this->keyword} in {$this->project->location}"
            : $this->keyword;

        $results = $firecrawlService->search($searchQuery, 5);

        $this->discoveryRun->increment('total_urls', count($results));
        $this->discoveryRun->increment('urls_found', count($results));

        $filterJobs = [];
        foreach ($results as $result) {
            $source = DiscoverySource::create([
                'project_id' => $this->project->id,
                'keyword' => $this->keyword,
                'url' => $result['url'],
                'title' => $result['title'],
            ]);

            $filterJobs[] = new FilterJob($this->project, $this->discoveryRun, $source, $this->signal);
        }

        if (count($filterJobs) > 0 && $this->batch()) {
            $this->batch()->add($filterJobs);
        } elseif (count($filterJobs) > 0) {
            foreach ($filterJobs as $job) {
                dispatch($job);
            }
        }
    }
}
