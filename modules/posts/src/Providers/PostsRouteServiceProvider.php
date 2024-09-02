<?php

namespace Modules\Posts\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

class PostsRouteServiceProvider extends RouteServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(__DIR__ . '/../../routes/posts-api.php');
        });
    }
}
