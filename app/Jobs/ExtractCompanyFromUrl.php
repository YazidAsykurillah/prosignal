<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\Company;
use App\Models\DiscoveryRun;
use App\Services\Firecrawl\FirecrawlService;
use App\Services\AI\Providers\GeminiProvider;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExtractCompanyFromUrl implements ShouldQueue
{
    use Batchable, Queueable;

    public function __construct(
        public Project $project,
        public DiscoveryRun $discoveryRun,
        public string $url,
        public string $keyword,
        public string $signal
    ) {}

    public function handle(FirecrawlService $firecrawlService, GeminiProvider $geminiProvider): void
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        // Sleep to avoid Gemini free tier rate limit (15 RPM)
        sleep(4);

        $content = $firecrawlService->crawl($this->url);
        
        if (!$content) {
            return;
        }

        $companyInfo = $geminiProvider->extractCompanyInfo($content, $this->signal);

        if (empty($companyInfo['company_name'])) {
            return;
        }

        $company = Company::firstOrCreate(
            ['company_name' => $companyInfo['company_name']],
            [
                'location' => $companyInfo['location'],
            ]
        );

        $this->project->companies()->syncWithoutDetaching([$company->id]);

        $this->project->discoveries()->create([
            'company_id' => $company->id,
            'source_url' => $this->url,
            'keyword' => $this->keyword,
            'signal' => $companyInfo['opportunity_signal'] ?? $this->signal,
            'summary' => $companyInfo['summary'] ?? '',
            'confidence_score' => $companyInfo['confidence_score'] ?? 0,
        ]);

        $this->discoveryRun->increment('total_companies');
        
        activity()
            ->performedOn($company)
            ->event('discovered')
            ->log('Company discovered via Firecrawl');
    }
}
