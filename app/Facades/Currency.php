<?php
namespace App\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Interfaces\CurrencyConversionInterface converter($name = null) 
 * @method static array convert($sourceCurrency, $destinationCurrency, $amount)
 *
 * @see \Illuminate\Http\Client\Factory
 */
class Currency extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'currency';
    }
}