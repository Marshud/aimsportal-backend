<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectOtherIdentifierOwnerOrg extends Model
{
    use HasFactory;

    protected $table = 'project_other_identifier_org';

    public function narratives()
    {
        return $this->morphMany(ProjectNarrative::class, 'element');
    }

    public function other_identifier()
    {
        return $this->belongsTo(ProjectOtherIdentifier::class, 'project_other_identifier_id');
    }
}
