<?php

namespace App\Services\AI\Providers;

use App\Contracts\AIProviderInterface;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements AIProviderInterface
{
    protected string $model;

    public function __construct()
    {
        $this->model = config('ai.providers.gemini.model', 'gemini-2.5-flash');
    }

    public function getName(): string
    {
        return 'gemini';
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function generate(array $payload): array
    {
        if (!isset($payload['prompt'])) {
            throw new \InvalidArgumentException('Payload must contain a prompt.');
        }

        try {
            $response = Gemini::generativeModel($this->model)->generateContent($payload['prompt']);
            
            return [
                'raw_response' => $response->text(),
                'provider' => $this->getName(),
                'model' => $this->getModel(),
            ];
        } catch (\Exception $e) {
            Log::error('Gemini Provider Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
