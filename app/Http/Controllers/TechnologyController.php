<?php

namespace App\Http\Controllers;

use App\Models\Technology;
use Illuminate\Support\Facades\Log;

class TechnologyController extends Controller
{
    public function index()
    {
        try {
            $technologies = Technology::where('is_active', true)
                ->orderBy('display_order')
                ->orderBy('name')
                ->get();
        } catch (\Throwable $e) {
            Log::warning('Technology index query failed', ['error' => $e->getMessage()]);
            $technologies = collect();
        }

        return view('technologies.index', compact('technologies'))
            ->with('pageType', 'technologies');
    }
}
