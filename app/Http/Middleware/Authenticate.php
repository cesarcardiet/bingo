<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            if ($request->is('agent/*')) {
                return route('agent.login.form');
            } elseif ($request->is('player/*')) {
                return route('player.login.form');
            } else {
                return '/';
            }
        }
    }
    
    
}
