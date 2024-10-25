<?php

namespace App\Http\Middleware;

use Closure;

class ClickjackingProtection
{
    public function handle($request, Closure $next)
    {
        \Log::info('ClickjackingProtection middleware executed');
        
        header('X-Frame-Options: DENY');  
        header("Content-Security-Policy: frame-ancestors 'self';");
        return $next($request);
    }
}
