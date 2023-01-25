<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\CurrencyConversionInterface;
use App\Services\OpenExchangeRatesService;

class ExchangeRateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CurrencyConversionInterface::class, function($app){
            $defaultProvider = get_system_setting('default_exchange_rates_provider') ?? 'open_exchange';
            $availableProviders = config('exchange-rate-providers.providers');
            
            if (in_array($defaultProvider, array_keys($availableProviders)))
            {
                $service = $availableProviders[$defaultProvider]['service'];
                $classPath = 'App\Services\\'.$service;

                if (!class_exists($classPath)) {
                    return $app->make(OpenExchangeRatesService::class);
                }
                
                return $app->make($classPath);
            }
            return $app->make(OpenExchangeRatesService::class);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
