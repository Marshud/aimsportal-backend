<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProjectTag extends Model implements Auditable
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
        $vocabulary = $this->iati_vocabulary();
        if ($vocabulary && $vocabulary->code == '2') {
            return iati_get_code_value('UNSDG-Goals', $this->code);
        }

        if ($vocabulary && $vocabulary->code == '3') {
            return iati_get_code_value('UNSDG-Targets', $this->code);
        }
        
       return iati_get_code_value($vocabulary->name, $this->code);
    }

    public function iati_vocabulary()
    {
        return iati_get_code_value('TagVocabulary', $this->vocabulary);
    }
}
