<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProjectResultIndicator extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = ['id'];

    protected $with = ['title_narratives', 'audits'];

    public function documents()
    {
        return $this->morphMany(ProjectDocumentLink::class, 'element');
    }

    public function title_narratives()
    {
        return $this->morphMany(ProjectNarrative::class, 'element');
    }
}
