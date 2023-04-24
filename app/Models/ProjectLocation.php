<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectLocation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['state', 'county', 'payam'];

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
