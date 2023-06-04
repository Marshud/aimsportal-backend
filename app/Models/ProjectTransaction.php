<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProjectTransaction extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $with = ['sectors', 'provider_org', 'receiver_org', 'aid_types', 'audits'];

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
