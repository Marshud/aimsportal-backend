<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function payams()
    {
        return $this->hasMany(Payam::class, 'county_id');
    }
}
