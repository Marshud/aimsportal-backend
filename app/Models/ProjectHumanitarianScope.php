<?php

namespace App\Models;

use App\Traits\HasIatiCodes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectHumanitarianScope extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['narratives'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function narratives()
    {
        return $this->morphMany(ProjectNarrative::class, 'element');
    }

    public function iati_types()
    {
        return iati_get_code_options('HumanitarianScopeType');
    }

    public function iati_type()
    {
       return iati_get_code_value('HumanitarianScopeType', $this->type);
    }

    public function iati_vocabulary()
    {
        return iati_get_code_value('HumanitarianScopeVocabulary', $this->vocabulary);
    }

    
}
