<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProjectLocation extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $with = ['state', 'county', 'payam', 'audits'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function county()
    {
        return $this->belongsTo(County::class, 'county_id');
    }

    public function payam()
    {
        return $this->belongsTo(Payam::class, 'payam_id');
    }
}
