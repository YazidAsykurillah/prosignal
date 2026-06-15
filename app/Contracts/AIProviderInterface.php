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
     * Generate content based on the provided payload.
     * The payload typically contains a 'prompt' and optionally 'system_instruction' or other params.
     */
    public function generate(array $payload): array;
}
