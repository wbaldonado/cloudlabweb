<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('contact', function (Request $request) {
            $limited = function (Request $request, array $headers) {
                $message = __('messages.contact.too_many');
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['ok' => false, 'message' => $message], 429, $headers);
                }

                $locale = $request->route('locale');
                $locale = is_string($locale) && in_array($locale, ['es', 'en'], true) ? $locale : 'es';
                $target = $request->input('return_to') === 'home'
                    ? url('/'.$locale).'#contacto'
                    : url('/'.$locale.'/contact');

                return redirect()->to($target)->with('error', $message);
            };

            return [
                Limit::perMinute(2)->by('contact-minute:'.$request->ip())->response($limited),
                Limit::perHour(6)->by('contact-hour:'.$request->ip())->response($limited),
            ];
        });

        RateLimiter::for('blog-interaction', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
