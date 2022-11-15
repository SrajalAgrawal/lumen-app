<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class adminCheck
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

            if($user->role == 1){
                return $next($request);
            }
        return response("You are not an Admin",401);
   
    }
}