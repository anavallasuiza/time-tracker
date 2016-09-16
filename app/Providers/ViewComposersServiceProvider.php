<?php


namespace App\Providers;


use App\View\Composers\SubHeaderViewComposer;
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
            'sub-header',
            'web.molecules.sub-header'
        ], SubHeaderViewComposer::class);

    }
}