<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Unit;

class OpenAIController extends Controller
{
    public function generateRecipe(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
        ]);

        $productName = trim($request->input('product_name'));

        // Fetch all unit abbreviations (we'll allow all except tsp/tbsp)
        $units = Unit::orderBy('type')->orderBy('name')->get(['abbreviation']);
        $allowedAbbrs = $units->pluck('abbreviation')
            ->filter()
            ->unique()
            ->map(fn($a) => strtolower($a))
            ->values()
            ->all();

        // Explicitly remove teaspoon and tablespoon
        $blacklist = ['tsp', 'tbsp'];
        $allowedAbbrs = array_values(array_filter($allowedAbbrs, fn($a) => ! in_array($a, $blacklist)));

        $allowedList = implode(', ', $allowedAbbrs);

        if (empty($allowedAbbrs)) {
            return back()
                ->withErrors(['openai' => 'No permitted units available to generate the recipe.'])
                ->withInput(['product_name' => $productName]);
        }

        $prompt = <<<PROMPT
I need a recipe to produce "{$productName}". List the ingredients needed (with quantities and units) and then describe the production steps in clear, complete sentences. 
Only use the following units of measure (by abbreviation): {$allowedList}. Do NOT use "tsp" or "tbsp" anywhere; if a typical recipe would call for teaspoons or tablespoons, convert those amounts into equivalent values using the allowed units (for example grams or milliliters). 
Do not output JSON, tables, or code-style formatting. Write it as a friendly, human-readable recipe with ingredient lines and narrative steps.
PROMPT;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an assistant that provides production recipes using only allowed units.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
            'max_tokens' => 700,
        ]);

        if ($response->failed()) {
            return back()
                ->withErrors(['openai' => 'Failed to reach OpenAI. Please try again.'])
                ->withInput(['product_name' => $productName]);
        }

        $resultText = $response->json('choices.0.message.content');

        if (! $resultText) {
            return back()
                ->withErrors(['openai' => 'OpenAI returned an unexpected response.'])
                ->withInput(['product_name' => $productName]);
        }

        return back()
            ->with('recipe', trim($resultText))
            ->with('recipe_product', $productName)
            ->withInput(['product_name' => $productName]);
    }
}
