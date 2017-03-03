<?php

namespace SM\N11;

use Illuminate\Support\ServiceProvider;

class N11ServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('n11', function(){
          return new N11(config('app.n11'));
        });
    }
}
