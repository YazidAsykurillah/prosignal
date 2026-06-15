<?php

namespace App\Services\AI;

use App\Contracts\AIProviderInterface;
use App\Services\AI\Providers\GeminiProvider;

class AiProviderFactory
{
    public static function make(): AIProviderInterface
    {
        $providerName = config('ai.default', 'gemini');

        return match ($providerName) {
            'gemini' => app(GeminiProvider::class),
            // 'openai' => app(OpenAiProvider::class), // For future implementation
            default => throw new \InvalidArgumentException("Unsupported AI provider: {$providerName}"),
        };
    }
}
