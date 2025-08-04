<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Production Planner</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <nav class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center h-16">
            <div class="flex items-center gap-6">
                <div class="text-xl font-bold text-indigo-700">@ProductionPlanner</div>
                <a href="{{ route('dashboard') }}" class="text-sm hover:underline">Planner</a>
                <a href="{{ route('inventory-items.index') }}" class="text-sm hover:underline">Inventory</a>
                <a href="{{ route('products.index') }}" class="text-sm hover:underline">Products</a>
                <a href="{{ route('productions.index') }}" class="text-sm hover:underline">Productions</a>
            </div>
            <div class="text-sm">Guest</div>
        </div>
    </nav>

    <main class="pt-6">
        <div class="max-w-7xl mx-auto px-4">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
