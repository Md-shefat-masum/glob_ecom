<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class DemoMode
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
        if (env('DEMO_MODE') == true && auth()->user()->email == 'demo@example.com') {
            if ($request->method() === 'POST') {
                Toastr::error("You're using Demo Mode","You cannot change content");
                return back();
            }
            return $next($request);
        } else {
            return $next($request);
        }
    }
}
