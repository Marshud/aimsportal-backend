<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Response::macro('success',function($data){
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        });

        Response::macro('error',function($error,$status_code,$error_data=[]){
            return response()->json([
                'success' => false,
                'error' => $error,
                'errors' => $error_data,
            ],$status_code);
        });
    }
}
