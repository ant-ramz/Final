<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Production;

class DashboardController extends Controller
{
    public function index()
    {
        $productions = Production::with('product')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->take(10)
            ->get();

        return view('dashboard.index', compact('productions'));
    }
}
