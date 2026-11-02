<?php

namespace App\Http\Controllers;

class CookiesController extends Controller
{
    public function index()
    {
        return view('cookies.index')
            ->with('pageType', 'cookies');
    }
}
