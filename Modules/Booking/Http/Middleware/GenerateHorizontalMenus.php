<?php

namespace Modules\Booking\Http\Middleware;

use App\Trait\HorizontalMenu;
use Closure;
use Illuminate\Http\Request;

class GenerateHorizontalMenus
{
    use HorizontalMenu;

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        \Menu::make('horizontal_menu', function ($menu) {
            $company = $this->createCompanyMenu($menu);

            // $this->childMain($company, [
            //     'title' => 'Bookings',
            //     'route' => 'backend.bookings.index',
            //     'active' => ['app/bookings*'],
            //     'order' => 220,
            // ]);
        });

        return $next($request);
    }
}
