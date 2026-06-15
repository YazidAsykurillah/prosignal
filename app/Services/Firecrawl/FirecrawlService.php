<?php

namespace App\Services\Firecrawl;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirecrawlService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.firecrawl.dev/v1';

    public function __construct()
    {
        $this->apiKey = config('services.firecrawl.key') ?? '';
    }

    public function search(string $query, int $limit = 5): array
    {
        if (empty($this->apiKey)) {
            Log::warning('Firecrawl API key is missing.');
            return [];
        }

        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/search", [
                'query' => $query,
                'limit' => $limit,
            ]);

        if ($response->failed()) {
            Log::error('Firecrawl search failed', ['error' => $response->json(), 'query' => $query]);
            return [];
        }

        $data = $response->json();
        
        // Extract URLs and titles from the search results
        return collect($data['data'] ?? [])->map(function ($item) {
            return [
                'url' => $item['url'] ?? null,
                'title' => $item['title'] ?? null,
            ];
        })->filter(fn ($item) => !empty($item['url']))->values()->toArray();
    }

    public function crawl(string $url): ?string
    {
        if (empty($this->apiKey)) {
            Log::warning('Firecrawl API key is missing.');
            return null;
        }

        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/scrape", [
                'url' => $url,
                'formats' => ['markdown']
            ]);

        if ($response->failed()) {
            Log::error('Firecrawl scrape failed', ['error' => $response->json(), 'url' => $url]);
            return null;
        }

        $data = $response->json();
        
        $markdown = $data['data']['markdown'] ?? '';
        $title = $data['data']['metadata']['title'] ?? '';
        $description = $data['data']['metadata']['description'] ?? '';

        if (empty($markdown) && empty($title) && empty($description)) {
            return null;
        }

        $reducedMarkdown = mb_substr($markdown, 0, 2000);

        return trim("Title: {$title}\nMeta Description: {$description}\n\nContent:\n{$reducedMarkdown}");
    }
}
