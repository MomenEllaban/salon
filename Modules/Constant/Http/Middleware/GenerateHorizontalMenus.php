<?php

namespace Modules\Constant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GenerateHorizontalMenus
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
