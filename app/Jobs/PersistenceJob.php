<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\Company;
use App\Models\DiscoveryRun;
use App\Models\DiscoverySource;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PersistenceJob implements ShouldQueue
{
    use Batchable, Queueable;

    public function __construct(
        public Project $project,
        public DiscoveryRun $discoveryRun,
        public DiscoverySource $source,
        public array $companyInfo,
        public string $signal
    ) {}

    public function handle(): void
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        $companyName = $this->companyInfo['company_name'] ?? null;
        if (!$companyName) {
            return;
        }

        $company = Company::firstOrCreate(
            ['company_name' => $companyName],
            [
                'location' => $this->companyInfo['location'] ?? null,
            ]
        );

        $this->project->companies()->syncWithoutDetaching([$company->id]);

        $this->project->discoveries()->create([
            'company_id' => $company->id,
            'source_url' => $this->source->url,
            'keyword' => $this->source->keyword,
            'signal' => $this->companyInfo['opportunity_signal'] ?? $this->signal,
            'summary' => $this->companyInfo['summary'] ?? '',
            'confidence_score' => $this->companyInfo['confidence_score'] ?? 0,
        ]);

        $this->discoveryRun->increment('total_companies');
        $this->discoveryRun->increment('companies_found');
        
        activity()
            ->performedOn($company)
            ->event('discovered')
            ->log('Company discovered via Firecrawl and AI Batch Extraction');
    }
}
