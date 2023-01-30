<?php

namespace App\Services;

use App\Interfaces\CurrencyConversionInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FixerCurrencyExchangeService implements CurrencyConversionInterface
{
    public function convert(string $sourceCurrency, string $destinationCurrency, float $amount = 1) :?float
    {
        $appId = get_system_setting('fixer_api_key') ?? '12345678';
        $url = "https://api.apilayer.com/fixer/latest?base=".$sourceCurrency."&symbols=".$destinationCurrency;
       
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'apikey' => $appId
            ])->get($url);
            if($response->failed()) {
                Log::error(['EXCHANGE_RATE_ERROR' => $response->body()]);
                return 0.0;
            }
            
            if($response->successful())
            {
                $result = $response->json();
                $rate = $result['rates'][$destinationCurrency] ?? $result['symbols'][$destinationCurrency] ?? 0.0;
                return round($rate*$amount, 2);
            }
            return 0.0;
            
        } catch(Throwable $e) {
            Log::error(['EXCHANGE_RATE_ERROR' => $e->getMessage()]);
            return 0.0;
        }
    }
}