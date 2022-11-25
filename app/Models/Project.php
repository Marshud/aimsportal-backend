<?php

namespace App\Models;

use App\Traits\HasMeta;
use App\Traits\HasOrganisation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, HasOrganisation, HasMeta;

    protected $guarded =[];

}
