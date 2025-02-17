<?php

namespace Modules\Customer\Http\Middleware;

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
            // Customers
            // $this->childMain($menu, [
            //     'icon' => '<i class="nav-icon fa-regular fa-sun"></i>',
            //     'title' => 'Staff',
            //     'route' => ['backend.customers.index'],
            //     'active' => 'app/customers*',
            //     'order' => 250,
            // ]);
        })->sortBy('order');

        return $next($request);
    }
}
