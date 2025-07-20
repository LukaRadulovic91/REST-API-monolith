<?php

namespace App\Http\Middleware;

use Closure;

class SetUserIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(empty(request('user_id'))) request()->merge(['user_id' => request()->user()->id]);
        return $next($request);
    }
}
