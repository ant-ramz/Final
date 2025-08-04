@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Schedule Production</h1>
        <a href="{{ route('productions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 rounded text-sm">Back</a>
    </div>

    <div class="card bg-white p-6 rounded-lg shadow">
        <form method="POST" action="{{ route('productions.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Product</label>
                <select name="product_id" required class="w-full border rounded px-3 py-2">
                    <option value="">Select product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Quantity</label>
                    <input name="quantity" type="number" min="1" value="{{ old('quantity', 1) }}" required class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Scheduled At</label>
                    <input name="scheduled_at" type="datetime-local" value="{{ old('scheduled_at') }}" required class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Schedule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
