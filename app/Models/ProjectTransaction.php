<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['sectors', 'provider_org', 'receiver_org', 'aid_types'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function sectors()
    {
        return $this->hasMany(ProjectTransactionSector::class, 'project_transaction_id');
    }

    public function provider_org()
    {
        return $this->hasOne(ProjectTransactionProviderOrg::class, 'project_transaction_id');
    }

    public function receiver_org()
    {
        return $this->hasOne(ProjectTransactionReceiverOrg::class, 'project_transaction_id');
    }

    public function aid_types()
    {
        return $this->hasMany(ProjectTransactionAidType::class, 'project_transaction_id');
    }
    
}
