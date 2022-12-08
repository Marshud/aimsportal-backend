<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectOtherIdentifier extends Model
{
    use HasFactory;

    protected $table = 'project_other_identifier';

    protected $guarded = [];

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
