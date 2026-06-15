<?php

namespace App\Services\AI\Prompts;

use App\Models\Project;

class MarketIntelligencePrompt
{
    public static function build(Project $project): string
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

    public static function parse(string $rawResponse): array
    {
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
}
