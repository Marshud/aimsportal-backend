<?php

namespace App\Http\Controllers\Api;

use App\Interfaces\CurrencyConversionInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CurrencyConvertController
{
    public $converter;

    public function __construct(CurrencyConversionInterface $converter)
    {
        $this->converter = $converter;
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'source_currency' => ["required", Rule::in(iati_get_code_options('Currency')->pluck('code'))],
            'destination_currency' => ["required", "different:source_currency", Rule::in(iati_get_code_options('Currency')->pluck('code'))],
            'amount' => 'required|numeric'
        ]);        

        if ($validator->fails()) {
			
			return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }
        $convertedAmount = $this->converter->convert(
            sourceCurrency: $request->source_currency, 
            destinationCurrency: $request->destination_currency, 
            amount: $request->amount
        );

        return response()->success($convertedAmount);
    }
}