<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProjectOtherIdentifierOwnerOrg extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $table = 'project_other_identifier_org';

    protected $guarded = ['id'];

    protected $with = ['narratives', 'audits'];

    public function narratives()
    {
        return $this->morphMany(ProjectNarrative::class, 'element');
    }

    public function other_identifier()
    {
        return $this->belongsTo(ProjectOtherIdentifier::class, 'project_other_identifier_id');
    }
}
