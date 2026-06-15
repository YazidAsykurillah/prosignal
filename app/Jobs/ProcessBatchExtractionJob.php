<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\DiscoveryRun;
use App\Models\DiscoverySource;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Throwable;

class ProcessBatchExtractionJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Project $project,
        public DiscoveryRun $discoveryRun,
        public string $signal
    ) {}

    public function handle(): void
    {
        $sources = DiscoverySource::where('project_id', $this->project->id)
            ->where('analyzed', false)
            ->whereNotNull('crawled_at')
            ->get();

        if ($sources->isEmpty()) {
            $this->discoveryRun->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            return;
        }

        $chunks = $sources->chunk(5);
        $jobs = [];

        foreach ($chunks as $chunk) {
            // chunk is a Collection of DiscoverySource
            $jobs[] = new ExtractionJob($this->project, $this->discoveryRun, $chunk, $this->signal);
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
            ->name('Extraction Run - Project ' . $this->project->id)
            ->dispatch();
    }
}
