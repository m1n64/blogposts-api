<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
	{
	}

    /**
     * @return void
     */
    public function boot(): void
	{
        $this->app->register(UserRouteServiceProvider::class);
	}
}
