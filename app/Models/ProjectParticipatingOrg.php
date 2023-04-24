<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectParticipatingOrg extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['organisation'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function iati_type()
    {
       return iati_get_code_value('OrganisationType', $this->type);
    }

    public function iati_role()
    {
        return iati_get_code_value('OrganisationRole', $this->role);
    }

    public function iati_crs_channel()
    {
        return iati_get_code_value('CRSChannelCode', $this->role);
    }

    
}
