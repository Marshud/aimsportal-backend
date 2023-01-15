<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTransactionProviderOrg extends Model
{
    use HasFactory;

    protected $table = 'project_transaction_provider_org';

    public function transaction()
    {
        return $this->belongsTo(ProjectTransaction::class, 'project_transaction_id');
    }

    public function narratives()
    {
        return $this->morphMany(ProjectNarrative::class, 'element');
    }

    public function iati_type()
    {
       return iati_get_code_value('OrganisationType', $this->type);
    }
}
