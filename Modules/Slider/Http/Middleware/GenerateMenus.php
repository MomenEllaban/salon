<?php

namespace Modules\Slider\Http\Middleware;

use App\Trait\HorizontalMenu;
use Closure;
use Illuminate\Http\Request;

class GenerateMenus
{
    use HorizontalMenu;

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /*
         *
         * Module Menu for Admin Backend
         *
         * *********************************************************************
         */
        \Menu::make('horizontal_menu', function ($menu) {
            // Sliders
            // $this->childMain($menu, [
            //     'title' => 'Sliders',
            //     'route' => ['backend.sliders.index'],
            //     'active' => 'app/sliders*',
            //     'order' => 150,
            // ]);
        })->sortBy('order');

        return $next($request);
    }
}
