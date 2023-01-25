<?php

return [
    'providers' => [
        'open_exchange' => [
            'service' => 'OpenExchangeRateService'
        ],
        'free_currency_api' => [
            'service' => 'FreeCurrencyApiService'
        ],
        'fixer' => [
            'service' => 'FixerCurrencyExchangeService'
        ]
    ]
];