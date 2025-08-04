@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Production Planner</h1>
        <a href="{{ route('productions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded shadow hover:bg-indigo-700">
            Schedule Production
        </a>
    </div>

    <div class="card bg-white p-6 rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-3 text-left">Product</th>
                    <th class="p-3 text-left">Qty</th>
                    <th class="p-3 text-left">Scheduled At</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productions as $p)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $p->product->name }}</td>
                        <td class="p-3">{{ $p->quantity }}</td>
                        <td class="p-3">{{ $p->scheduled_at->format('Y-m-d H:i') }}</td>
                        <td class="p-3">
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded {{ $p->status === 'planned' ? 'bg-yellow-100 text-yellow-800' : ($p->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : ($p->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td class="p-3 flex gap-4">
                            <a href="{{ route('productions.show', $p) }}" class="text-indigo-600 hover:underline text-sm">View</a>
                            <a href="{{ route('productions.delete', $p) }}" class="text-red-600 hover:underline text-sm">Delete</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">No production runs scheduled.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
