<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProjectOtherIdentifier extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = 'project_other_identifier';

    protected $guarded = [];

    protected $with = ['audits'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function iati_type()
    {
       return iati_get_code_value('OtherIdentifierType', $this->type);
    }

    public function owner_org()
    {
        return $this->hasOne(ProjectOtherIdentifierOwnerOrg::class, 'project_other_identifier_id');
    }
}
