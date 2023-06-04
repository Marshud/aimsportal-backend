<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProjectResult extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $with = ['title_narratives', 'audits'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function documents()
    {
        return $this->morphMany(ProjectDocumentLink::class, 'element');
    }

    public function title_narratives()
    {
        return $this->morphMany(ProjectNarrative::class, 'element');
    }

}
