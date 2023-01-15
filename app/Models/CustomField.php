<?php

namespace App\Models;

use App\Traits\HasOrganisation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CustomField extends Model implements Auditable
{
    use HasFactory, HasOrganisation, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    public function options()
    {
        return $this->hasMany(CustomFieldOption::class, 'custom_field_id');
    }

    protected $appends = ['audits'];
}
