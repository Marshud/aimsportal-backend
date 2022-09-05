<?php

namespace App\Models;

use Laratrust\Models\LaratrustTeam;

class Organisation extends LaratrustTeam
{
    public $guarded = [];

    public function category()
    {
        return $this->belongsTo(OrganisationCategory::class,'category_id');
    }

    public function users()
    {
        return $this->hasMany(User::class,'current_organisation_id');
    }

    public function hasData() : bool
    {
        return false;
    }
}
