<?php

namespace KS\Dakik;

use Illuminate\Support\ServiceProvider;

class DakikServiceProvider extends ServiceProvider
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
        $this->app->bind('dakik', function(){
          return new Dakik(config('app.dakik'));
        });
    }
}
