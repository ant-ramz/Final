@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">Delete Production</h1>
            <p class="text-sm text-gray-600">
                You are about to delete the production run for <strong>{{ $production->product->name }}</strong> scheduled at <strong>{{ $production->scheduled_at->format('Y-m-d H:i') }}</strong> (Quantity: {{ $production->quantity }}).
            </p>
        </div>
        <a href="{{ route('productions.show', $production) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 rounded text-sm">Back</a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow border border-red-200">
        <div class="mb-4">
            <p class="text-red-700">
                <strong>Warning:</strong> This action cannot be undone. Deleting this production will remove its scheduling and any associated advice.
            </p>
        </div>
        <form method="POST" action="{{ route('productions.destroy', $production) }}">
            @csrf
            @method('DELETE')
            <div class="flex gap-4">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Delete Production
                </button>
                <a href="{{ route('productions.show', $production) }}" class="inline-flex items-center gap-2 px-5 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

