<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductionRequest;
use App\Models\Production;
use App\Models\Product;
use App\Models\User;
use App\Http\Services\UnitConverter;
use App\Http\Services\OpenAIRecipeService;
use Illuminate\Support\Facades\Auth;

class ProductionController extends Controller
{
    public function index()
    {
        $productions = Production::with('product')->orderBy('scheduled_at')->get();
        return view('productions.index', compact('productions'));
    }

    public function create()
    {
        $products = Product::all();
        return view('productions.create', compact('products'));
    }

    public function store(ProductionRequest $request, UnitConverter $converter, OpenAIRecipeService $openAI)
    {
        //Fallback user if none exists (since auth may be disabled)
        $userId = Auth::id();
        if (is_null($userId)) {
            $systemUser = User::firstOrCreate(
                ['email' => 'system@local'],
                ['name' => 'System', 'password' => bcrypt('password')]
            );
            $userId = $systemUser->id;
        }

        $production = Production::create([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'scheduled_at' => $request->scheduled_at,
            'user_id' => $userId,
        ]);

        $shortages = $this->computeShortages($production, $converter);

        $requiredComponents = [];
        $currentInventory = [];
        foreach ($shortages as $s) {
            $requiredComponents[] = [
                'name' => $s['name'],
                'needed_qty' => $s['required_display'],
                'unit' => $s['display_unit'],
            ];
            $currentInventory[] = [
                'name' => $s['name'],
                'available_qty' => $s['available_display'],
                'unit' => $s['display_unit'],
            ];
        }

        $aiAdviceText = $openAI->adviseShortagesPlainText($requiredComponents, $currentInventory);

        return view('productions.show', [
            'production' => $production->load('product.components.inventoryItem', 'product.components.unit'),
            'shortages' => $shortages,
            'aiAdviceText' => $aiAdviceText,
        ]);
    }

    public function show(Production $production, UnitConverter $converter, OpenAIRecipeService $openAI)
    {
        $production->load('product.components.inventoryItem', 'product.components.unit');
        $shortages = $this->computeShortages($production, $converter);

        $requiredComponents = [];
        $currentInventory = [];
        foreach ($shortages as $s) {
            $requiredComponents[] = [
                'name' => $s['name'],
                'needed_qty' => $s['required_display'],
                'unit' => $s['display_unit'],
            ];
            $currentInventory[] = [
                'name' => $s['name'],
                'available_qty' => $s['available_display'],
                'unit' => $s['display_unit'],
            ];
        }

        $aiAdviceText = $openAI->adviseShortagesPlainText($requiredComponents, $currentInventory);

        return view('productions.show', compact('production', 'shortages', 'aiAdviceText'));
    }

    public function delete(Production $production)
    {
        $production->load('product');
        return view('productions.delete', compact('production'));
    }

    public function destroy(Production $production)
    {
        $production->delete();
        return redirect()->route('productions.index')->with('success', 'Production deleted.');
    }

    private function computeShortages(Production $production, UnitConverter $converter): array
    {
        $product = $production->product()->with('components.inventoryItem', 'components.unit')->first();
        $quantityToMake = $production->quantity;

        $shortages = [];

        foreach ($product->components as $component) {
            $requiredPerUnit = $component->quantity;
            $unit = $component->unit;
            $inventoryItem = $component->inventoryItem;

            $requiredBase = $converter->toBase($requiredPerUnit * $quantityToMake, $unit);
            $availableBase = $inventoryItem->quantity_in_base;
            $shortageBase = max(0, $requiredBase - $availableBase);

            $requiredDisplay = $converter->fromBase($requiredBase, $unit);
            $availableDisplay = $converter->fromBase($availableBase, $unit);
            $shortageDisplay = $converter->fromBase($shortageBase, $unit);

            $shortages[] = [
                'name' => $inventoryItem->name,
                'required_display' => round($requiredDisplay, 4),
                'available_display' => round($availableDisplay, 4),
                'shortage_display' => round($shortageDisplay, 4),
                'display_unit' => $unit->abbreviation,
            ];
        }

        return $shortages;
    }
}
