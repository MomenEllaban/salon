<?php

namespace Modules\Employee\Http\Middleware;

use App\Trait\Menu;
use Closure;

class GenerateMenus
{
    use Menu;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*
         *
         * Module Menu for Admin Backend
         *
         * *********************************************************************
         */
        \Menu::make('menu', function ($menu) {
            // $this->childMain($menu, [
            //     'title' => 'Staff',
            //     'route' => ['backend.employees.index'],
            //     'active' => 'app/user-role/staff',
            //     'order' => 250,
            // ]);
        })->sortBy('order');

        return $next($request);
    }
}
