<?php

namespace App\Providers;

use App\Yelp\Client;
use Illuminate\Support\ServiceProvider;

class YelpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            return new Client(env('YELP_API_KEY'));
        });
    }
}
