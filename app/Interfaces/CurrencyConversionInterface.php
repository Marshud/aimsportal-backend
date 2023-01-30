<?php

namespace App\Interfaces;

interface CurrencyConversionInterface
{
    public function convert(string $sourceCurrency, string $destinationCurrency, float $amount) :?float;
}