<?php

namespace App\Services;

use App\Interfaces\CurrencyConversionInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class OpenExchangeRatesService implements CurrencyConversionInterface
{
    public function convert(string $sourceCurrency, string $destinationCurrency, float $amount) :?float
    {
        $appId = get_system_setting('open_exchange_app_id') ?? '12345678';        
        $url = "https://openexchangerates.org/api/latest.json?app_id=".$appId."&base=".$sourceCurrency."&symbols=".$destinationCurrency;
       
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json'
            ])->get($url);
            if($response->failed()) {
                Log::error(['EXCHANGE_RATE_ERROR' => $response->body()]);
                return 0.0;
            }
            
            if($response->successful())
            {
                $result = $response->json();
                $rate = $result['rates'][$destinationCurrency] ?? 0.0;
                return round($rate*$amount, 2);
            }
            return 0.0;
            
        } catch(Throwable $e) {
            Log::error(['EXCHANGE_RATE_ERROR' => $e->getMessage()]);
            return 0.0;
        }
    }
}