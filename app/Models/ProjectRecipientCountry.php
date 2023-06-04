<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProjectRecipientCountry extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $with = ['narratives', 'audits'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function narratives()
    {
        return $this->morphMany(ProjectNarrative::class, 'element');
    }

    public function iati_code()
    {
       return iati_get_code_value('Country', $this->code);
    }
}
