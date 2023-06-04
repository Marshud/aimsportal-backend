<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProjectPlannedDisbursementProviderOrg extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = 'project_planned_disbursement_provider_org';

    protected $guarded = ['id'];

    protected $with = ['narratives', 'audits'];

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
