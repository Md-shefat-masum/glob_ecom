<?php

namespace App\Http\Middleware;

use App\Models\UserRolePermission;
use Closure;
use Illuminate\Http\Request;

class CheckUserType
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
        // return $next($request);
        if(auth()->user()->status == 0){
            return abort(401);
        } else {
            if (auth()->user()->user_type == 1) {
                return $next($request);

            } else if(auth()->user()->user_type == 2){
                $conditions = [
                    ['user_id',auth()->user()->id],
                    ['route','/'.$request->route()->uri()]
                ];
                $check = UserRolePermission::where($conditions)->first();
                //dd($check, $conditions);
                if($check){
                    return $next($request);
                } else {
                    return abort(401);
                }

            } else {
                return abort(401);
            }
        }

    }
}
