<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;

class Neo4jServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('neo4j', function ($app) {
            $config = config('neo4j');

            return ClientBuilder::create()
                ->withDriver('bolt', sprintf('bolt://%s:%d', $config['host'], $config['port']), Authenticate::basic($config['username'], $config['password']))
                ->build();
        });
    }

    /**
     * @return void
     */
    public function boot(): void
    {
    }
}
