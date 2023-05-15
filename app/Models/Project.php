<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasMeta;
use App\Traits\HasOrganisation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Project extends Model implements Auditable
{
    use HasUuids, HasFactory, HasMeta, \OwenIt\Auditing\Auditable;

    protected $guarded =[];

    protected $with = ['title_translations', 'reporting_organisation', 'reporting_organisation.category', 'humanitarian_scopes',
    'participating_organisations', 'project_descriptions', 'other_identifiers', 'activity_dates', 'recipient_countries',
    'recipient_regions', 'locations', 'sectors', 'tags', 'country_budget_items', 'policy_markers', 'default_aid_types',
    'budgets', 'planned_disbursements', 'transactions', 'audits'];

    public function title_translations()
    {
        return $this->morphMany(ProjectNarrative::class, 'element');
    }

    public function condition()
    {
        return $this->hasOne(ProjectCondition::class, 'project_id');
    }

    public function credit_report()
    {
        return $this->hasOne(ProjectCreditReport::class, 'project_id');
    }

    public function humanitarian_scopes()
    {
        return $this->hasMany(ProjectHumanitarianScope::class, 'project_id');
    }

    public function reporting_organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function participating_organisations()
    {
        return $this->hasMany(ProjectParticipatingOrg::class, 'project_id');
    }

    public function project_descriptions()
    {
        return $this->hasMany(ProjectDescription::class, 'project_id');
    }

    public function other_identifiers()
    {
        return $this->hasMany(ProjectOtherIdentifier::class, 'project_id');
    }

    public function activity_dates()
    {
        return $this->hasMany(ProjectActivityDate::class, 'project_id');
    }

    public function recipient_countries()
    {
        return $this->hasMany(ProjectRecipientCountry::class, 'project_id');
    }

    public function recipient_regions()
    {
        return $this->hasMany(ProjectRecipientRegion::class, 'project_id');
    }

    public function locations()
    {
        return $this->hasMany(ProjectLocation::class, 'project_id');
    }

    public function sectors()
    {
        return $this->hasMany(ProjectSector::class, 'project_id');
    }

    public function tags()
    {
        return $this->hasMany(ProjectTag::class, 'project_id');
    }

    public function country_budget_items()
    {
        return $this->hasMany(ProjectCountryBudgetItem::class, 'project_id');
    }

    public function policy_markers()
    {
        return $this->hasMany(ProjectPolicyMarker::class, 'project_id');
    }

    public function default_aid_types()
    {
        return $this->hasMany(ProjectDefaultAidType::class, 'project_id');
    }

    public function budgets()
    {
        return $this->hasMany(ProjectBudget::class, 'project_id');
    }

    public function planned_disbursements()
    {
        return $this->hasMany(ProjectPlannedDisbursement::class, 'project_id');
    }

    public function transactions()
    {
        return $this->hasMany(ProjectTransaction::class, 'project_id');
    }

}
