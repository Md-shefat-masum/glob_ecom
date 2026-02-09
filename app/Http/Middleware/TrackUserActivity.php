<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\UserActivity;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Track user activity by updating the last seen time in cache
        // if (Auth::check()) {
        //     $expiresAt = now()->addMinutes(5); // cache expires in 5 minutes
        //     Cache::put('user-is-online-' . Auth::id(), now(), $expiresAt);
        // }


        if (Auth::check()) {
            $userActivity = UserActivity::where('user_id', Auth::id())->first();

            if (!$userActivity || now()->diffInMinutes($userActivity->last_seen) >= 1) {
                UserActivity::updateOrCreate(
                    ['user_id' => Auth::id()],
                    ['last_seen' => now()]
                );
            }
        }

        return $next($request);
    }
}
