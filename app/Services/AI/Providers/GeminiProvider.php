<?php

namespace App\Services\AI\Providers;

use App\Contracts\AIProviderInterface;
use App\Models\Project;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements AIProviderInterface
{
    protected string $model;

    public function __construct()
    {
        $this->model = config('gemini.model', 'gemini-2.5-flash');
    }

    public function getName(): string
    {
        return 'gemini';
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function generateMarketIntelligence(Project $project, &$promptUsed = null): array
    {
        $prompt = $this->buildPrompt($project);
        $promptUsed = $prompt;

        try {
            $response = Gemini::generativeModel($this->model)->generateContent($prompt);
            $rawResponse = $response->text();

            return $this->parseResponse($rawResponse);
        } catch (\Exception $e) {
            Log::error('Gemini Provider Error: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function buildPrompt(Project $project): string
    {
        return <<<PROMPT
You are an expert B2B Market Intelligence AI.
Analyze the following project or business:

Name: {$project->name}
Industry: {$project->industry}
Description: {$project->description}
Location: {$project->location}

Generate a market intelligence report in strict JSON format matching exactly this structure:
{
    "industries": ["industry 1", "industry 2"],
    "roles": ["role 1", "role 2"],
    "company_sizes": ["Small", "Medium", "Large"],
    "opportunity_signals": ["signal 1", "signal 2"],
    "discovery_keywords": ["keyword 1", "keyword 2"]
}

Rules:
- Output ONLY valid JSON, no markdown blocks, no extra text.
- 'industries': Target industries that would buy this product/service.
- 'roles': Target job titles/roles of decision makers.
- 'company_sizes': Estimate the sizes of target companies.
- 'opportunity_signals': Events or signals indicating a need for this.
- 'discovery_keywords': Search keywords to find these companies.
PROMPT;
    }

    protected function parseResponse(string $rawResponse): array
    {
        // Strip markdown blocks if Gemini returns them despite the prompt
        $cleaned = preg_replace('/```json\s*/', '', $rawResponse);
        $cleaned = preg_replace('/```\s*/', '', $cleaned);
        $cleaned = trim($cleaned);

        $data = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse AI response as JSON: ' . json_last_error_msg());
        }

        return [
            'industries' => $data['industries'] ?? [],
            'roles' => $data['roles'] ?? [],
            'company_sizes' => $data['company_sizes'] ?? [],
            'opportunity_signals' => $data['opportunity_signals'] ?? [],
            'discovery_keywords' => $data['discovery_keywords'] ?? [],
            'raw_response' => $rawResponse,
        ];
    }

    public function extractCompanyInfo(string $content, string $signal): array
    {
        $prompt = $this->buildCompanyExtractionPrompt($content, $signal);

        try {
            $response = Gemini::generativeModel($this->model)->generateContent($prompt);
            $rawResponse = $response->text();

            return $this->parseCompanyExtractionResponse($rawResponse);
        } catch (\Exception $e) {
            Log::error('Gemini Provider Error (Extract Company): ' . $e->getMessage());
            return [];
        }
    }

    protected function buildCompanyExtractionPrompt(string $content, string $signal): string
    {
        // Truncate content to avoid exceeding token limits (rough approximation)
        $truncatedContent = substr($content, 0, 15000);

        return <<<PROMPT
You are an expert AI Data Extractor.
Analyze the following content extracted from a webpage and extract information about the company mentioned, especially focusing on whether they exhibit the following opportunity signal: "{$signal}".

Content:
{$truncatedContent}

Generate the response in strict JSON format matching exactly this structure:
{
    "company_name": "Company Name",
    "opportunity_signal": "The specific signal found in the text related to {$signal}",
    "location": "Company location if mentioned, otherwise null",
    "summary": "A brief summary of what the company does and why it matches the signal",
    "confidence_score": 85
}

Rules:
- Output ONLY valid JSON, no markdown blocks, no extra text.
- If no company is clearly identifiable, return empty or null values but keep the JSON structure.
- 'confidence_score' should be an integer from 0 to 100 indicating how confident you are that this company is a valid prospect matching the signal.
PROMPT;
    }

    protected function parseCompanyExtractionResponse(string $rawResponse): array
    {
        $cleaned = preg_replace('/```json\s*/', '', $rawResponse);
        $cleaned = preg_replace('/```\s*/', '', $cleaned);
        $cleaned = trim($cleaned);

        $data = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Failed to parse AI response as JSON for company extraction: ' . json_last_error_msg());
            return [];
        }

        return [
            'company_name' => $data['company_name'] ?? null,
            'opportunity_signal' => $data['opportunity_signal'] ?? null,
            'location' => $data['location'] ?? null,
            'summary' => $data['summary'] ?? null,
            'confidence_score' => $data['confidence_score'] ?? 0,
        ];
    }
}
