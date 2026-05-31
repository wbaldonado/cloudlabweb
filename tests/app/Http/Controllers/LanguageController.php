<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switchLanguage($locale)
    {
        if (in_array($locale, ['en', 'es'])) {
            Session::put('locale', $locale);
            Session::save();
            App::setLocale($locale);
        }

        // Redirect back to the previous page, or home if no referrer
        $previousUrl = url()->previous();
        if ($previousUrl && $previousUrl !== url()->current() && strpos($previousUrl, url('/')) === 0) {
            return redirect($previousUrl);
        }
        
        return redirect()->route('home');
    }
}
