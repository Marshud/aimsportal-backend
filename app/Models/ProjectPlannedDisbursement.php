<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProjectPlannedDisbursement extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $with = ['audits'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function provider_org()
    {
        return $this->hasOne(ProjectPlannedDisbursementProviderOrg::class, 'project_planned_disbursement_id');
    }

    public function receiver_org()
    {
        return $this->hasOne(ProjectPlannedDisbursementReceiverOrg::class, 'project_planned_disbursement_id');
    }

    public function iati_type()
    {
       return iati_get_code_value('BudgetType', $this->type);
    }
}
