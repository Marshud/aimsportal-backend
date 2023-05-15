<?php

namespace App\AuditResolvers;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\Resolver;

class OrganisationIdResolver implements Resolver
{

    public static function resolve(Auditable $auditable = null)
    {
        if(method_exists($auditable, 'organisation')) {
            return $auditable->organisation_id;
        }
        
        if (auth('sanctum')->check()) {
            return auth('sanctum')->user()->current_organisation_id;
        }

        return null;
    }
}