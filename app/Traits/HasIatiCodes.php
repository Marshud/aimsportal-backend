<?php

namespace App\Traits;

use App\Models\Codelist;
use App\Models\CustomField;
use App\Models\Meta;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

trait HasIatiCodes
{
    public function iati_get_code_options(string $codeName) : Collection
    {
        $codelist = Codelist::where('name', $codeName)->first();
        if ($codelist) {
            return $codelist->options;
        }
        return collect([]); 
    }

    public function iati_get_code_value(string $codeName, string $codeValue) : ?Collection
    {
        return $this->iati_get_code_options($codeName)->where('code', $codeValue)->first();
    }
}