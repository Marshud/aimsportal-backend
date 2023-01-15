<?php

use App\Models\Codelist;
use App\Models\CodelistOption;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


if (!function_exists('iati_get_code_options'))
{
    function iati_get_code_options(string $codeName) : Collection
    {
        $codelist = Codelist::where('name', $codeName)->first();
        if ($codelist) {
            return $codelist->options;
        }
        return collect([]); 
    }
}

if (!function_exists('iati_get_code_value'))
{
    function iati_get_code_value(?string $codeName, ?string $codeValue) : ?CodelistOption
    {
        if (null === $codeName || $codeValue === null) return null;
        return iati_get_code_options($codeName)->where('code', $codeValue)->first(); 
    }
}




