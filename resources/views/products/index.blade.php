@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Products</h1>
        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
            New Product
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-md bg-green-50 border border-green-200 p-4">
            <div class="text-green-700 text-sm">{{ session('success') }}</div>
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

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Components</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="border-t hover:bg-gray-50 align-top">
                        <td class="p-3 align-top">{{ $product->name }}</td>
                        <td class="p-3">
                            @if($product->components && $product->components->isNotEmpty())
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($product->components as $component)
                                        <li>
                                            {{ $component->inventoryItem?->name ?? '—' }}:
                                            {{ 
                                                // display quantity with unit abbreviation if available
                                                (float) round($component->quantity, 4)
                                            }}
                                            {{ $component->unit?->abbreviation ?? '' }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-500">No components defined.</span>
                            @endif
                        </td>
                        <td class="p-3 flex gap-2">
                            <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:underline text-sm">Edit</a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete {{ $product->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-4 text-center text-gray-500">No products yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
