@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 py-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">New Product</h1>
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 rounded text-sm">Back</a>
    </div>

    {{-- Flash / validation --}}
    @if(session('success'))
        <div class="rounded-md bg-indigo-600 border border-indigo-600 p-4">
            <div class="text-bg-indigo-600 text-sm">{{ session('success') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-md bg-red-50 border border-red-200 p-4">
            <ul class="list-disc list-inside text-red-700 text-sm">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('products.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Product Name</label>
                    <input name="name" type="text" required value="{{ old('name') }}" class="w-full border rounded px-3 py-2" placeholder="e.g., Chocolate Bar">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <input name="description" type="text" value="{{ old('description') }}" class="w-full border rounded px-3 py-2" placeholder="Optional description">
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-lg font-semibold">Components</h2>
                <div id="components-container" class="space-y-4">
                    @php
                        $existing = old('components', []);
                    @endphp

                    @if(count($existing) > 0)
                        @foreach($existing as $index => $comp)
                            <div class="component-row grid grid-cols-1 md:grid-cols-5 gap-3 items-end border rounded p-3 relative">
                                <div>
                                    <label class="block text-xs font-medium mb-1">Inventory Item</label>
                                    <select name="components[{{ $index }}][inventory_item_id]" required class="w-full border rounded px-2 py-2 text-sm">
                                        @foreach($inventoryItems as $it)
                                            <option value="{{ $it->id }}" {{ (int)($comp['inventory_item_id'] ?? 0) === $it->id ? 'selected' : '' }}>
                                                {{ $it->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium mb-1">Quantity</label>
                                    <input name="components[{{ $index }}][quantity]" type="number" step="any" required value="{{ $comp['quantity'] ?? '' }}" class="w-full border rounded px-2 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium mb-1">Unit</label>
                                    <select name="components[{{ $index }}][unit_id]" required class="w-full border rounded px-2 py-2 text-sm">
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ (int)($comp['unit_id'] ?? 0) === $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }} ({{ $unit->abbreviation }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-xs font-medium mb-1">Remove</label>
                                    <button type="button" class="remove-component inline-flex px-3 py-2 bg-red-100 text-red-700 rounded text-sm">✕</button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="component-row grid grid-cols-1 md:grid-cols-5 gap-3 items-end border rounded p-3 relative">
                            <div>
                                <label class="block text-xs font-medium mb-1">Inventory Item</label>
                                <select name="components[0][inventory_item_id]" required class="w-full border rounded px-2 py-2 text-sm">
                                    @foreach($inventoryItems as $it)
                                        <option value="{{ $it->id }}">{{ $it->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1">Quantity</label>
                                <input name="components[0][quantity]" type="number" step="any" required class="w-full border rounded px-2 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1">Unit</label>
                                <select name="components[0][unit_id]" required class="w-full border rounded px-2 py-2 text-sm">
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-medium mb-1">&nbsp;</label>
                                <button type="button" class="remove-component inline-flex px-3 py-2 bg-red-100 text-red-700 rounded text-sm">✕</button>
                            </div>
                        </div>
                    @endif
                </div>

                <div>
                    <button type="button" id="add-component" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-600 text-sm">
                        + Add Component
                    </button>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded shadow hover:bg-indigo-700">
                    Create Product
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('click', function(e){
    if (e.target.matches('#add-component')) {
        const container = document.getElementById('components-container');
        const index = container.querySelectorAll('.component-row').length;
        const template = document.querySelector('.component-row');
        if (!template) return;
        const clone = template.cloneNode(true);

        clone.querySelectorAll('select, input').forEach(el => {
            if (el.name) {
                el.name = el.name.replace(/\d+/, index);
                if (el.tagName.toLowerCase() === 'input') {
                    el.value = '';
                }
            }
        });

        container.appendChild(clone);
    }

    if (e.target.matches('.remove-component')) {
        const row = e.target.closest('.component-row');
        if (row) row.remove();
    }
});
</script>
@endpush

@endsection
