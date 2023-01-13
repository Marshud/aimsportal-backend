<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

trait UsesIatiStandard
{

    public function getIatiAttribute($attribute)
    {
        //Log::error($attribute);
        // try {
        //     if(null !== (self::IATI_DEFINITION)) {
        //         return self::IATI_DEFINITION['enabled'];
        //     }
            
        // } catch(\Exception $e) {
        //     throw $e;
        // }
    }
}