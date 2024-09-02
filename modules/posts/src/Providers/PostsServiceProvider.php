<?php

namespace Modules\Posts\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Posts\Models\Post;
use Modules\Posts\Policies\Post\PostPolicy;

class PostsServiceProvider extends ServiceProvider
{
    /**
     * @var \class-string[]
     */
    protected $policies = [
        Post::class => PostPolicy::class,
    ];

    /**
     * @return void
     */
    public function boot(): void
	{
        $this->app->register(PostsRouteServiceProvider::class);
	}
}
