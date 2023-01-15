<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectDocumentLinkDescription extends Model
{
    use HasFactory;

    public function project_document()
    {
        return $this->belongsTo(ProjectDocumentLink::class, 'project_document_id');
    }

    public function narratives()
    {
        return $this->morphMany(ProjectNarrative::class, 'element');
    }
}
