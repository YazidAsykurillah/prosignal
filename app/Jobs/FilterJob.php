<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\DiscoveryRun;
use App\Models\DiscoverySource;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class FilterJob implements ShouldQueue
{
    use Batchable, Queueable;

    public function __construct(
        public Project $project,
        public DiscoveryRun $discoveryRun,
        public DiscoverySource $source,
        public string $signal
    ) {}

    public function handle(): void
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        // Duplicate URL Detection
        $existing = DiscoverySource::where('project_id', $this->project->id)
            ->where('url', $this->source->url)
            ->where('id', '<', $this->source->id)
            ->first();

        if ($existing) {
            // Duplicate found, skip processing
            return;
        }

        // Rule-Based Relevancy Filter
        $score = $this->calculateRelevanceScore($this->source->title, $this->source->url);
        $this->source->update(['relevance_score' => $score]);

        $threshold = 5; // Minimum threshold
        if ($score >= $threshold) {
            $this->discoveryRun->increment('urls_filtered');
            
            $crawlJob = new CrawlJob($this->project, $this->discoveryRun, $this->source, $this->signal);
            
            if ($this->batch()) {
                $this->batch()->add([$crawlJob]);
            } else {
                dispatch($crawlJob);
            }
        }
    }

    protected function calculateRelevanceScore(?string $title, string $url): int
    {
        $score = 0;
        $content = strtolower(($title ?? '') . ' ' . $url);

        $signals = [
            'factory expansion' => 10,
            'new plant' => 10,
            'investment' => 8,
            'production line' => 7,
            'capex' => 8,
            'facility upgrade' => 7,
            'manufacturing expansion' => 10,
            'plant construction' => 10,
            'new factory' => 10,
        ];

        foreach ($signals as $keyword => $points) {
            if (Str::contains($content, $keyword)) {
                $score += $points;
            }
        }

        // Fallback to custom signal if provided and no predefined matched (or just add)
        if (!empty($this->signal) && Str::contains($content, strtolower($this->signal))) {
            $score += 5;
        }

        return $score;
    }
}
