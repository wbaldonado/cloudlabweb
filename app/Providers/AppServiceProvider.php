<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();

        if (session()->has('locale') && \App\Support\LocalizedUrl::isLocale(session('locale'))) {
            app()->setLocale(session('locale'));
        }

        \Illuminate\Support\Facades\URL::defaults([
            'locale' => \App\Support\LocalizedUrl::isLocale(app()->getLocale())
                ? app()->getLocale()
                : config('seo.default_locale', 'es'),
        ]);
    }
}
