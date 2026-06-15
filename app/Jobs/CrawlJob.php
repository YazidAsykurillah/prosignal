<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\DiscoveryRun;
use App\Models\DiscoverySource;
use App\Services\Firecrawl\FirecrawlService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CrawlJob implements ShouldQueue
{
    use Batchable, Queueable;

    public function __construct(
        public Project $project,
        public DiscoveryRun $discoveryRun,
        public DiscoverySource $source,
        public string $signal
    ) {}

    public function handle(FirecrawlService $firecrawlService): void
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        $content = $firecrawlService->crawl($this->source->url);

        if (!$content) {
            return;
        }

        $this->source->update([
            'content' => $content,
            'crawled_at' => now(),
        ]);
    }
}
