<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Services\UnitConverter;
use Illuminate\Database\QueryException;

class InventoryItemController extends Controller
{
    public function index()
    {
        $items = InventoryItem::with('defaultUnit')->get();
        $units = Unit::all();
        return view('inventory.index', compact('items', 'units'));
    }

    public function create()
    {
        $units = Unit::all();
        return view('inventory.create', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'default_unit_id' => 'required|exists:units,id',
        ]);

        InventoryItem::create([
            'name' => $request->name,
            'default_unit_id' => $request->default_unit_id,
            'quantity_in_base' => 0,
        ]);

        return redirect()->route('inventory-items.index')->with('success', 'Inventory item created.');
    }

    public function edit(InventoryItem $inventoryItem)
    {
        $units = Unit::all();
        return view('inventory.edit', compact('inventoryItem', 'units'));
    }

    public function update(Request $request, InventoryItem $inventoryItem, UnitConverter $converter)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'default_unit_id' => 'required|exists:units,id',
            'quantity' => 'nullable|numeric',
        ]);

       
        $inventoryItem->name = $request->name;
        $inventoryItem->default_unit_id = $request->default_unit_id;

        
        if ($request->filled('quantity')) {
            $unit = Unit::findOrFail($request->default_unit_id);
            $inventoryItem->quantity_in_base = $converter->normalize($request->quantity, $unit);
        }

        $inventoryItem->save();

        return redirect()->route('inventory-items.index')->with('success', 'Inventory item updated.');
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $count = $inventoryItem->productComponents()->count();
        if ($count > 0) {
            return back()->withErrors([
                "Cannot delete '{$inventoryItem->name}' because it's used in {$count} product component(s). Remove or reassign those first."
            ]);
        }

        try {
            $inventoryItem->delete();
           
            return redirect()->route('inventory-items.index')->with('success', 'Deleted.');
        } catch (QueryException $e) {
            return back()->withErrors([
                "Failed to delete '{$inventoryItem->name}'. It may still be referenced elsewhere."
            ]);
        }
    }

    public function addStock(Request $request, UnitConverter $converter)
    {
        $request->validate([
            'quantity' => 'required|numeric',
            'unit_id' => 'required|exists:units,id',
            'inventory_item_name' => 'required|string|max:255',
        ]);

        $unit = Unit::findOrFail($request->unit_id);
        $name = trim($request->inventory_item_name);

        $item = InventoryItem::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();
        if (! $item) {
            $item = InventoryItem::create([
                'name' => $name,
                'default_unit_id' => $request->unit_id,
                'quantity_in_base' => 0,
            ]);
        }

        $addedBase = $converter->normalize($request->quantity, $unit);
        $item->quantity_in_base = bcadd($item->quantity_in_base, $addedBase, 8);
        $item->save();

        return back()->with('success', 'Stock added.');
    }
}
