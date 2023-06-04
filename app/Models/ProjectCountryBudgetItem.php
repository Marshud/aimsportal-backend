<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProjectCountryBudgetItem extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = 'project_budget_items';

    protected $guarded = [];

    protected $with = ['description_narratives', 'audits'];


    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function description_narratives()
    {
        return $this->morphMany(ProjectNarrative::class, 'element');
    }

    public function iati_code()
    {
       return iati_get_code_value('BudgetIdentifier', $this->code);
    }
}
