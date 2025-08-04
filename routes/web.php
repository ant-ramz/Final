<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\OpenAIController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('inventory-items', InventoryItemController::class)->except(['show']);
Route::post('inventory-items/add-stock', [InventoryItemController::class, 'addStock'])->name('inventory.addStock');

Route::resource('products', ProductController::class);

Route::get('productions/{production}/delete', [ProductionController::class, 'delete'])->name('productions.delete');
Route::resource('productions', ProductionController::class);

Route::post('openai/generate-recipe', [OpenAIController::class, 'generateRecipe'])->name('openai.generateRecipe');
Route::post('openai/shortage-advice', [OpenAIController::class, 'shortageAdvice'])->name('openai.shortageAdvice');

Route::fallback(fn() => redirect()->route('dashboard'));

