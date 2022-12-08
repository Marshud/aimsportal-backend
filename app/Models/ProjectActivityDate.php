<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectActivityDate extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function iati_type()
    {
       return iati_get_code_value('ActivityDateType', $this->type);
    }

    public function narratives()
    {
        return $this->morphMany(ProjectNarrative::class, 'element');
    }
}
