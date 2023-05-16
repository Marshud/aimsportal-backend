<?php

return [
    'system_variables' => [

        [
            'name' => 'default_exchange_rates_provider',
            'options' => ['open_exchange', 'free_currency_api', 'fixer'],
        ],

        [
            'name' => 'free_currency_api_api_key',
            'options' => null,
        ],

        [
            'name' => 'open_exchange_app_id',
            'options' => null,
        ],

        [
            'name' => 'fixer_api_key',
            'options' => null,
        ],
        [
            'name' => 'maximum_organisation_users',
            'options' => null,
        ],
        [
            'name' => 'months_to_keep_project_changes',
            'options' => ['1', '3', '6', '12'],
        ]

    ]

];