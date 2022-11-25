<?php

namespace App\Models;

use App\Traits\UsesIatiStandard;
use Laratrust\Models\LaratrustTeam;
use OwenIt\Auditing\Contracts\Auditable;

class Organisation extends LaratrustTeam implements Auditable
{
    use UsesIatiStandard;
    use \OwenIt\Auditing\Auditable;
    public $guarded = [];

    // const IATI_DEFINITION = [
    //     'enabled' => true,
    //     'version' => '2.0.3',
    //     'api' => 'https://github.com/IATI/IATI-Codelists-NonEmbedded/tree/master/xml/'
    // ];

    const IATI_COLUMNS = [
        'name' => [
            'api' => false,
            'code' => false,
        ],
        'iati_identifier' => [

        ],
        'category'
    ];

    public function category()
    {
        return $this->belongsTo(OrganisationCategory::class,'category_id');
    }

    public function team_users()
    {
        return $this->hasMany(User::class,'current_organisation_id');
    }

    public function hasData() : bool
    {
        if($this->team_users()->exists()) {
            return true;
        }
        return false;
    }
}
