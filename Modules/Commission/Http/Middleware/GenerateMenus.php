<?php

namespace Modules\Commission\Http\Middleware;

use Closure;

class GenerateMenus
{
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
            // // Commissions
            // $menu->add('<i class="nav-icon fa-regular fa-sun"></i> <span class="item-name">'.__('Commissions').'</span>', [
            //     'route' => 'backend.commissions.index',
            //     'class' => 'nav-item',
            // ])
            // ->data([
            //     'order'         => 77,
            //     'activematches' => ['app/commissions*'],
            //     'permission'    => ['view_commissions'],
            // ])
            // ->link->attr([
            //     'class' => 'nav-link',
            // ]);
        })->sortBy('order');

        return $next($request);
    }
}
