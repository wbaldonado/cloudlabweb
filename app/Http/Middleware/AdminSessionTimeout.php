<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSessionTimeout
{
    /**
     * Number of seconds of inactivity before timing out the session.
     */
    protected int $timeout = 300; // 5 minutes

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_admin) {
            $lastActivity = $request->session()->get('admin_last_activity');
            $nowTimestamp = now()->timestamp;

            if ($lastActivity && ($nowTimestamp - $lastActivity) > $this->timeout) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('admin.login')
                    ->with('error', __('Your session has expired due to inactivity. Please log in again.'));
            }

            $request->session()->put('admin_last_activity', $nowTimestamp);
        }

        return $next($request);
    }
}
