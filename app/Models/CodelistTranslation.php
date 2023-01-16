<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodelistTranslation extends Model
{
    use HasFactory;

    public function codelist_option()
    {
        return $this->belongsTo(CodelistOption::class, 'codelist_option_id');
    }
}
