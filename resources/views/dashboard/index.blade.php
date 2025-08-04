@extends('layouts.app')

@section('content')
<div class="space-y-8">
    {{-- Upcoming Productions Section --}}
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold mb-1">Dashboard</h1>
                <p class="text-lg font-semibold">Upcoming Productions</p>
            </div>
        </div>

        <div class="mt-4">
            @if(isset($productions) && $productions->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-2 text-left">Product</th>
                                <th class="p-2 text-left">Qty</th>
                                <th class="p-2 text-left">Scheduled At</th>
                                <th class="p-2 text-left">Status</th>
                                <th class="p-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productions as $p)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="p-2">{{ $p->product?->name }}</td>
                                    <td class="p-2">{{ $p->quantity }}</td>
                                    <td class="p-2">{{ optional($p->scheduled_at)->format('Y-m-d H:i') }}</td>
                                    <td class="p-2">
                                        <span class="inline-block px-2 py-1 text-xs font-medium rounded {{ $p->status === 'planned' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($p->status) }}
                                        </span>
                                    </td>
                                    <td class="p-2 flex gap-2">
                                        <a href="{{ route('productions.show', $p) }}" class="text-indigo-600 hover:underline text-sm">View</a>
                                        <form method="POST" action="{{ route('productions.destroy', $p) }}" onsubmit="return confirm('Delete production?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 text-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-gray-500">No upcoming productions.</div>
            @endif
        </div>
    </div>

    {{-- OpenAI Recipe Section (separated) --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-2">Generate Recipe via OpenAI</h2>
        <div class="bg-gray-50 rounded-md p-6 space-y-4">
            <form method="POST" action="{{ route('openai.generateRecipe') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                @csrf
                <div class="md:col-span-2">
                    <label for="product_name" class="block font-medium text-sm mb-1">Product Name</label>
                    <input
                        id="product_name"
                        name="product_name"
                        type="text"
                        required
                        placeholder="e.g., Chocolate Chip Cookie"
                        value="{{ old('product_name') }}"
                        class="w-full border rounded px-3 py-2"
                    />
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        Generate Recipe
                    </button>
                </div>
            </form>

            {{-- Errors --}}
            @if($errors->has('product_name') || $errors->has('openai'))
                <div class="bg-red-50 border border-red-200 rounded p-3 text-sm text-red-700">
                    @foreach($errors->all() as $err)
                        <div>{{ $err }}</div>
                    @endforeach
                </div>
            @endif

            {{-- Generated recipe --}}
            @if(session('recipe'))
                <div class="mt-2 bg-white rounded shadow p-4">
                    <h3 class="text-lg font-semibold mb-2">
                        Recipe for "{{ session('recipe_product', old('product_name', request('product_name'))) }}"
                    </h3>
                    <div class="prose whitespace-pre-wrap text-sm">
                        {{ session('recipe') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
