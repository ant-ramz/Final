@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">Production: {{ $production->product->name }}</h1>
            <p class="text-sm text-gray-600">
                Quantity: {{ $production->quantity }} — Scheduled: {{ $production->scheduled_at->format('Y-m-d H:i') }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('productions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 rounded text-sm">Back</a>
            <a href="{{ route('productions.delete', $production) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded text-sm hover:bg-red-600">
                Delete
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Inventory comparison -->
        <div class="card bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-3">Inventory Comparison</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 text-left">Component</th>
                            <th class="p-3 text-left">Required</th>
                            <th class="p-3 text-left">Available</th>
                            <th class="p-3 text-left">Shortage</th>
                            <th class="p-3 text-left">Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shortages as $s)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3">{{ $s['name'] }}</td>
                                <td class="p-3">{{ $s['required_display'] }}</td>
                                <td class="p-3">{{ $s['available_display'] }}</td>
                                <td class="p-3">{{ $s['shortage_display'] }}</td>
                                <td class="p-3">{{ $s['display_unit'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- AI advice summary -->
        <div class="card bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-3">AI Advice Summary</h2>
            @if(!empty($aiAdviceText))
                <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 rounded text-sm">
                    {{ $aiAdviceText }}
                </div>
            @else
                <div class="text-red-600">No advice available.</div>
            @endif
        </div>
    </div>
</div>
@endsection
