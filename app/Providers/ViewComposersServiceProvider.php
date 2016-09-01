<?php


namespace App\Providers;


use Illuminate\Support\ServiceProvider;

class ViewComposersServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->app->view->composer([
            'sub-header'
        ], \App\View\Composers\SubHeaderViewComposer::class);
    }
}