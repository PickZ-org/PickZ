<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param mixed ...$roles permitted roles
     * @return mixed
     */

    public function handle($request, Closure $next, ... $roles)
    {
        if(!Auth::check()) {
            abort(401, 'This action is unauthorized.');
        }
        if(!Auth::user()->hasAnyRole($roles)) {
            abort(401, 'This action is unauthorized.');
        }
        return $next($request);
    }
}
