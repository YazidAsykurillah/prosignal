<?php

namespace App\Services\AI\Prompts;

use Illuminate\Support\Facades\Log;

class BatchedCompanyExtractionPrompt
{
    public static function build(array $sources, string $signal): string
    {
        $jsonSources = json_encode($sources);
        
        return <<<PROMPT
You are an expert AI Data Extractor.
Analyze the following batched content extracted from multiple webpages. For each source, extract information about the company mentioned, especially focusing on whether they exhibit the following opportunity signal: "{$signal}".

Sources:
{$jsonSources}

Generate the response in strict JSON format matching exactly this structure (an array of objects):
[
    {
        "source_id": 1, // ID from the input source
        "company_name": "Company Name",
        "opportunity_signal": "The specific signal found in the text related to {$signal}",
        "location": "Company location if mentioned, otherwise null",
        "summary": "A brief summary of what the company does and why it matches the signal",
        "confidence_score": 85
    }
]

Rules:
- Output ONLY valid JSON, no markdown blocks, no extra text.
- Return an array of objects corresponding to the input sources. Include the `source_id` to map back.
- If no company is clearly identifiable for a source, return empty or null values but keep the JSON structure and include the `source_id`.
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
            Log::error('Failed to parse batched AI response as JSON for company extraction: ' . json_last_error_msg());
            return [];
        }

        return $data ?? [];
    }
}
