<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Codelist extends Model
{
    use HasFactory;

    public function options()
    {
        return $this->hasMany(CodelistOption::class, 'codelist_id');
    }
}
