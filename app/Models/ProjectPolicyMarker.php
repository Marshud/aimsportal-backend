<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProjectPolicyMarker extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $with = ['narratives', 'audits'];

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
       return iati_get_code_value('PolicyMarker', $this->code);
    }

    public function iati_vocabulary()
    {
        return iati_get_code_value('PolicyMarkerVocabulary', $this->vocabulary);
    }

    public function iati_significance()
    {
        return iati_get_code_value('PolicySignificance', $this->significance);
    }
}
