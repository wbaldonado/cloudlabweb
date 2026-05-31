<?php

namespace App\Http\Controllers;

use App\Models\Technology;

class TechnologyController extends Controller
{
    public function index()
    {
        $technologies = Technology::where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view('technologies.index', compact('technologies'))
            ->with('pageType', 'technologies');
    }
}
