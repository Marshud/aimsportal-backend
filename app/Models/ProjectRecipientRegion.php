<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectRecipientRegion extends Model
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

    public function iati_code()
    {
       return iati_get_code_value('Region', $this->code);
    }

    public function iati_vocabulary()
    {
        return iati_get_code_value('RegionVocabulary', $this->vocabulary);
    }
}
