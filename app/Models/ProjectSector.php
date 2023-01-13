<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSector extends Model
{
    use HasFactory;

    protected $guarded = [];

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
        //todo return Sector or SectorCategory or UNSDG-Goals or UNSDG-targets based on vocabulary chosen
       return iati_get_code_value('Sector', $this->code);
    }

    public function iati_vocabulary()
    {
        return iati_get_code_value('SectorVocabulary', $this->vocabulary);
    }
}
