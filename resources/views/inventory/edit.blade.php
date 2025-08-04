@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Edit Inventory Item</h1>
        <a href="{{ route('inventory-items.index') }}" class="inline-flex px-4 py-2 bg-gray-200 rounded text-sm">Back</a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-4">
            <div class="text-green-700 text-sm">{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 p-4">
            <ul class="list-disc list-inside text-red-700 text-sm">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded shadow p-6 space-y-6">
        <form method="POST" action="{{ route('inventory-items.update', $inventoryItem) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">Name</label>
                <input name="name" type="text" required value="{{ old('name', $inventoryItem->name) }}" class="w-full border rounded px-3 py-2" placeholder="e.g., Flour">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Default Unit</label>
                <select name="default_unit_id" required class="w-full border rounded px-3 py-2">
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" {{ (int) old('default_unit_id', $inventoryItem->default_unit_id) === $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->abbreviation }})
                        </option>
                    @endforeach
                </select>
            </div>

            @php
                $displayQty = 0;
                if ($inventoryItem->defaultUnit) {
                    $converter = new \App\Http\Services\UnitConverter();
                    $displayQty = $converter->fromBase($inventoryItem->quantity_in_base, $inventoryItem->defaultUnit);
                }
            @endphp

            <div>
                <label class="block text-sm font-medium mb-1">Quantity (in default unit)</label>
                <input
                    name="quantity"
                    type="number"
                    step="any"
                    value="{{ old('quantity', round($displayQty, 6)) }}"
                    class="w-full border rounded px-3 py-2"
                    placeholder="Leave blank to keep existing quantity"
                >
                <p class="text-xs text-gray-500 mt-1">Editing this will overwrite the current total stock (converted from the default unit to base).</p>
            </div>

            <div>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Save
                </button>
            </div>
        </form>
        <div>
            <form method="POST" action="{{ route('inventory-items.destroy', $inventoryItem) }}" onsubmit="return confirm('Delete this item?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
