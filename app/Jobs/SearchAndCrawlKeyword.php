<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\DiscoveryRun;
use App\Services\Firecrawl\FirecrawlService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SearchAndCrawlKeyword implements ShouldQueue
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

        $urls = $firecrawlService->search($searchQuery, 5);

        $this->discoveryRun->increment('total_urls', count($urls));

        $jobs = [];
        foreach ($urls as $url) {
            $jobs[] = new ExtractCompanyFromUrl($this->project, $this->discoveryRun, $url, $this->keyword, $this->signal);
        }

        if (count($jobs) > 0 && $this->batch()) {
            $this->batch()->add($jobs);
        } elseif (count($jobs) > 0) {
            // Fallback if not run in batch
            foreach ($jobs as $job) {
                dispatch($job);
            }
        }
    }
}
