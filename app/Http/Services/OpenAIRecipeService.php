<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIRecipeService
{
    protected string $endpoint = 'https://api.openai.com/v1/chat/completions';
    protected string $model = 'gpt-4';

    //Generates recipe

    public function generateRecipe(string $productName): ?array
    {
        $prompt = "I want to create a product called '{$productName}'. "
            . "Provide a recipe: list the inventory items required to make one unit of this product. "
            . "For each item, give a name, quantity, and reasonable unit (e.g., grams, liters, pieces). "
            . "Return the result as a JSON array of objects with keys: name, quantity, unit. "
            . "Example:\n"
            . '[{"name":"Flour","quantity":200,"unit":"g"}, {"name":"Sugar","quantity":50,"unit":"g"}]';

        $response = Http::withToken(config('services.openai.key'))
            ->post($this->endpoint, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 800,
            ]);

        if (!$response->successful()) {
            Log::error('OpenAI recipe generation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $body = $response->json();
        $content = $body['choices'][0]['message']['content'] ?? '';

        $parsed = $this->extractJson($content);
        if (is_array($parsed)) {
            return $parsed;
        }

        Log::warning('OpenAI recipe response could not be parsed as JSON', ['raw' => $content]);
        return null;
    }

   
    public function adviseShortages(array $requiredComponents, array $currentInventory): ?array
    {
        $prompt = "Given the following production requirements and current inventory, "
            . "identify which items are in shortage and how much needs to be ordered. "
            . "Production requirements per planned run: " . json_encode($requiredComponents) . ". "
            . "Current inventory: " . json_encode($currentInventory) . ". "
            . "Output a JSON array of objects with keys: name, shortage, unit, suggested_order_qty.";

        $response = Http::withToken(config('services.openai.key'))
            ->post($this->endpoint, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.2,
                'max_tokens' => 600,
            ]);

        if (!$response->successful()) {
            Log::error('OpenAI shortage advice failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $body = $response->json();
        $content = $body['choices'][0]['message']['content'] ?? '';

        $parsed = $this->extractJson($content);
        if (is_array($parsed)) {
            return $parsed;
        }

        Log::warning('OpenAI shortage advice response could not be parsed as JSON', ['raw' => $content]);
        return null;
    }

    
    public function adviseShortagesPlainText(array $requiredComponents, array $currentInventory): ?string
    {
        $prompt = "I have production requirements and current inventory. "
            . "Production requirements per planned run: " . json_encode($requiredComponents) . ". "
            . "Current inventory: " . json_encode($currentInventory) . ". "
            . "In plain English, using complete sentences (no JSON, no bullet-code formatting), explain what items are in shortage, by how much, and what should be ordered to fulfill the planned production. "
            . "Be concise and clear. Example: 'You are short of Flour by 49 kg, so order 49 kg to meet the planned production.'";

        $response = Http::withToken(config('services.openai.key'))
            ->post($this->endpoint, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
                'max_tokens' => 300,
            ]);

        if (!$response->successful()) {
            Log::error('OpenAI plain-text shortage advice failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $body = $response->json();
        $content = $body['choices'][0]['message']['content'] ?? '';

        return trim($content);
    }

    
    private function extractJson(string $content): ?array
    {
        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/(\[.*\])/sU', $content, $matches)) {
            $attempt = json_decode($matches[1], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($attempt)) {
                return $attempt;
            }
        }

        return null;
    }
}
