<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProjectDocumentLinkCategory extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $with = ['audits'];

    public function project_document()
    {
        return $this->belongsTo(ProjectDocumentLink::class, 'project_document_id');
    }

    
}
