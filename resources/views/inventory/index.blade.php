@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold">Inventory</h1>
    <a href="{{ route('inventory-items.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded">New Item</a>
</div>

@if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded">
        <div class="text-green-800 text-sm">{{ session('success') }}</div>
    </div>
@endif

@if($errors->any())
    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">
        <ul class="text-red-700 text-sm list-disc list-inside">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded shadow p-6">
        <h2 class="font-semibold mb-3">Add / Update Stock</h2>
        <form method="POST" action="{{ route('inventory.addStock') }}" class="flex flex-wrap gap-4">
            @csrf
            <div class="flex flex-col">
                <label class="text-sm mb-1">Item</label>
                <input list="inventory-items" name="inventory_item_name" required class="border rounded px-2 py-1" placeholder="Type or pick" value="{{ old('inventory_item_name') }}">
                <datalist id="inventory-items">
                    @foreach($items as $item)
                        <option value="{{ $item->name }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div class="flex flex-col">
                <label class="text-sm mb-1">Quantity</label>
                <input name="quantity" type="number" step="any" required class="border rounded px-2 py-1" value="{{ old('quantity') }}">
            </div>
            <div class="flex flex-col">
                <label class="text-sm mb-1">Unit</label>
                <select name="unit_id" required class="border rounded px-2 py-1">
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->abbreviation }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Add</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded shadow p-6 overflow-x-auto">
        <h2 class="font-semibold mb-3">Current Stock</h2>
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Name</th>
                    <th class="p-2 text-left">Quantity</th>
                    <th class="p-2 text-left">Default Unit</th>
                    <th class="p-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="border-t">
                        <td class="p-2">{{ $item->name }}</td>
                        <td class="p-2">{{ round($item->display_quantity,4) }}</td>
                        <td class="p-2">{{ $item->defaultUnit?->abbreviation }}</td>
                        <td class="p-2 flex gap-2 items-center">
                            <a href="{{ route('inventory-items.edit', $item) }}" class="text-indigo-600">Edit</a>

                            <form method="POST" action="{{ route('inventory-items.destroy', $item) }}" onsubmit="return confirm('Delete {{ $item->name }}?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">No inventory items yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
