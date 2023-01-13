<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectDocumentLink extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function title_narratives()
    {
        return $this->hasMany(ProjectDocumentLinkTitle::class, 'project_document_id');
    }

    public function description_narratives()
    {
        return $this->hasMany(ProjectDocumentLinkDescription::class, 'project_document_id');
    }

    public function categories()
    {
        return $this->hasMany(ProjectDocumentLinkCategory::class, 'project_document_id');
    }

    public function languages()
    {
        return $this->morphMany(UsedLanguage::class, 'element');
    }

    public function element()
    {
        return $this->morphTo();
    }
}
