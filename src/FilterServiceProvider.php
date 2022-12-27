<?php

namespace Tomodo531\FilterableFilters;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FilterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('filterable-filters', __DIR__.'/../dist/js/filter.js');
            Nova::style('filterable-filters', __DIR__.'/../dist/css/filter.css');
        });

        Route::middleware(['nova'])
            ->prefix('nova-vendor/filterable-filters')
            ->group(__DIR__ . '/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
