<?php

namespace App\Http\Controllers;

use App\Support\LocalizedUrl;

class LanguageController extends Controller
{
    /**
     * Legacy session switcher. Sends the visitor to the same path with a locale prefix.
     */
    public function switchLanguage(string $locale)
    {
        if (! LocalizedUrl::isLocale($locale)) {
            $locale = config('seo.default_locale', 'es');
        }

        return redirect()->to(LocalizedUrl::swap($locale, url()->previous()));
    }
}
