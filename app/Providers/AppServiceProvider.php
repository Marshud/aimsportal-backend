<?php

namespace App\Providers;

use App\Http\Resources\CodelistTranslationResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);
        Response::macro('success',function($data,$message='success'){
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ]);
        });

        Response::macro('error',function($error,$status_code,$error_data=[]){
            return response()->json([
                'success' => false,
                'message' => $error,
                'errors' => $error_data,
            ],$status_code);
        });

       // CodelistTranslationResource::withoutWrapping();
        Model::preventLazyLoading(! $this->app->isProduction());

        Sanctum::$accessTokenAuthenticationCallback = function ($accessToken, $isValid) {
            return !$accessToken->last_used_at || $accessToken->last_used_at->gte(now()->subMinutes(config('sanctum.expiration')));
        };
    }
}
