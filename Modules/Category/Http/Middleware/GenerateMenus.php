<?php

namespace Modules\Category\Http\Middleware;

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
            //   'icon' => ' <i class="nav-icon fa-regular fa-sun"></i>',
            //   'title' => __('Category',
            //   'route' => 'backend.categories.index',
            //   'active' => ['app/categories'],
            //   'order' => 120,
            // ]);

            // $this->childMain($menu, [
            //   'icon' => ' <i class="nav-icon fa-regular fa-sun"></i>',
            //   'title' => __('Sub-Category'),
            //   'route' => 'backend.categories.index_nested',
            //   'active' => ['app/sub-categories'],
            //   'order' => 120,
            // ]);
        })->sortBy('order');

        return $next($request);
    }
}
