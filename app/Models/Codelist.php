<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Codelist extends Model
{
    use HasFactory;

    protected $guarded =[];

    public function options()
    {
        return $this->hasMany(CodelistOption::class, 'codelist_id');
    }

    public function translations()
    {
        return $this->hasManyThrough(CodelistTranslation::class, CodelistOption::class, 'codelist_id', 'codelist_option_id');
    }
}
