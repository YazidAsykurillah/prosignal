<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\DiscoveryRun;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Throwable;

class ProcessProjectDiscovery implements ShouldQueue
{
    use Queueable;

    public function __construct(public Project $project, public DiscoveryRun $discoveryRun)
    {
    }

    public function handle(): void
    {
        $this->discoveryRun->update(['status' => 'processing', 'started_at' => now()]);

        $marketIntel = $this->project->marketIntelligence;
        if (!$marketIntel) {
            $this->discoveryRun->update(['status' => 'failed', 'completed_at' => now()]);
            return;
        }

        $keywords = $marketIntel->discovery_keywords ?? [];
        $signals = $marketIntel->opportunity_signals ?? [];
        
        $signal = !empty($signals) ? implode(', ', $signals) : 'Any opportunity';

        // Limit to 5 keywords for MVP
        $keywords = array_slice($keywords, 0, 5);

        $this->discoveryRun->update(['total_keywords' => count($keywords)]);

        $jobs = [];
        foreach ($keywords as $keyword) {
            $jobs[] = new SearchAndCrawlKeyword($this->project, $this->discoveryRun, $keyword, $signal);
        }

        $discoveryRunId = $this->discoveryRun->id;

        Bus::batch($jobs)
            ->then(function (\Illuminate\Bus\Batch $batch) use ($discoveryRunId) {
                DiscoveryRun::find($discoveryRunId)?->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            })
            ->catch(function (\Illuminate\Bus\Batch $batch, Throwable $e) use ($discoveryRunId) {
                DiscoveryRun::find($discoveryRunId)?->update([
                    'status' => 'failed',
                    'completed_at' => now(),
                ]);
            })
            ->name('Discovery Run - Project ' . $this->project->id)
            ->dispatch();
    }
}
