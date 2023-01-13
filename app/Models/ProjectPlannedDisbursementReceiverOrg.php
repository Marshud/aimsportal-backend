<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectPlannedDisbursementReceiverOrg extends Model
{
    use HasFactory;
    
    protected $table = 'project_planned_disbursement_receiver_org';

    public function disbursement()
    {
        return $this->belongsTo(ProjectPlannedDisbursement::class, 'project_planned_disbursement_id');
    }

    public function narratives()
    {
        return $this->morphMany(ProjectNarrative::class, 'element');
    }

    public function iati_type()
    {
       return iati_get_code_value('OrganisationType', $this->type);
    }
}
