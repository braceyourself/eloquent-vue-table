<?php

namespace Braceyourself\EloquentVueTable;


use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

class EloquentVueTableRouteProvider extends RouteServiceProvider
{
    protected $namespace = "Braceyourself\EloquentVueTable\Http\Controllers";
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }





    public function map(){
        Route::prefix('api/braceyourself')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(__DIR__ . '/routes/routes.php');

    }
}
