<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodelistOption extends Model
{
    use HasFactory;

    protected $guarded =[];

    public function translations()
    {
        return $this->hasMany(CodelistTranslation::class, 'codelist_option_id');
    }
}
