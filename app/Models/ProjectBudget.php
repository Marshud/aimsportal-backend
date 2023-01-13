<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectBudget extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function iati_type()
    {
       return iati_get_code_value('BudgetType', $this->type);
    }

    public function iati_status()
    {
       return iati_get_code_value('BudgetStatus', $this->status);
    }
}
