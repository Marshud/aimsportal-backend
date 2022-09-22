<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisationCategory extends Model
{
    use HasFactory;
    public $guarded = [];

    public function organisations()
    {
        return $this->hasMany(Organisation::class,'category_id');
    }
}
