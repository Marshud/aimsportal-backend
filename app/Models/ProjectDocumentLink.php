<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProjectDocumentLink extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $with = ['title_narratives', 'categories', 'desription_narratives', 'audits'];

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
