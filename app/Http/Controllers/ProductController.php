<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryItem;
use App\Models\Unit;
use App\Models\ProductComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('components.inventoryItem', 'components.unit')->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $inventoryItems = InventoryItem::all();
        $units = Unit::all();
        return view('products.create', compact('inventoryItems', 'units'));
    }

    public function edit(Product $product)
    {
        $inventoryItems = InventoryItem::all();
        $units = Unit::all();
        $product->load('components');
        return view('products.edit', compact('product', 'inventoryItems', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'components' => 'required|array|min:1',
            'components.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'components.*.unit_id' => 'required|exists:units,id',
            'components.*.quantity' => 'required|numeric|min:0.0001',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            foreach ($validated['components'] as $comp) {
                ProductComponent::create([
                    'product_id' => $product->id,
                    'inventory_item_id' => $comp['inventory_item_id'],
                    'unit_id' => $comp['unit_id'],
                    'quantity' => $comp['quantity'],
                ]);
            }
        });

        return redirect()->route('products.index')->with('success', 'Product created.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'components' => 'required|array|min:1',
            'components.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'components.*.unit_id' => 'required|exists:units,id',
            'components.*.quantity' => 'required|numeric|min:0.0001',
        ]);

        DB::transaction(function () use ($validated, $product) {
            $product->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            $product->components()->delete();

            foreach ($validated['components'] as $comp) {
                ProductComponent::create([
                    'product_id' => $product->id,
                    'inventory_item_id' => $comp['inventory_item_id'],
                    'unit_id' => $comp['unit_id'],
                    'quantity' => $comp['quantity'],
                ]);
            }
        });

        return redirect()->route('products.edit', $product)->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->components()->delete();
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }
}
