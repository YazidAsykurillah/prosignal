<?php

namespace App\Contracts;

use App\Models\Project;

interface AIProviderInterface
{
    /**
     * Get the name of the provider (e.g., 'gemini', 'openai')
     */
    public function getName(): string;

    /**
     * Get the model being used (e.g., 'gemini-1.5-pro')
     */
    public function getModel(): string;

    /**
     * Generate Market Intelligence for a given project.
     * Must return an array with:
     * - 'industries'
     * - 'roles'
     * - 'company_sizes'
     * - 'opportunity_signals'
     * - 'discovery_keywords'
     * - 'raw_response'
     */
    public function generateMarketIntelligence(Project $project, &$promptUsed = null): array;
}
