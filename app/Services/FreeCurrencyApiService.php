<?php

namespace App\Services;

use App\Interfaces\CurrencyConversionInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FreeCurrencyApiService implements CurrencyConversionInterface
{
    public function convert(string $sourceCurrency, string $destinationCurrency, float $amount) :?float
    {
        $appId = get_system_setting('free_currency_api_api_key') ?? '12345678';        
        $url = "https://api.freecurrencyapi.com/v1/latest?apikey=".$appId."&base_currency=".$sourceCurrency."&currencies=".$destinationCurrency;
       
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
                $rate = $result['data'][$destinationCurrency] ?? 0.0;
                return round($rate*$amount, 2);
            }
            return 0.0;
        } catch(Throwable $e) {
            Log::error(['EXCHANGE_RATE_ERROR' => $e->getMessage()]);
            return 0.0;
        }
    }
}