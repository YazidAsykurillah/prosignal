<?php

namespace App\Services\AI\Prompts;

use Illuminate\Support\Facades\Log;

class CompanyExtractionPrompt
{
    public static function build(string $content, string $signal): string
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

    public static function parse(string $rawResponse): array
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
            'raw_response' => $rawResponse,
        ];
    }
}
