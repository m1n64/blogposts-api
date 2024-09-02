<?php

namespace Modules\User\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

class UserRouteServiceProvider extends RouteServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(__DIR__ . '/../../routes/user-api.php');
        });
    }
}
