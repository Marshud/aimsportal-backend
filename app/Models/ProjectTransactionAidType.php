<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProjectTransactionAidType extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $with = ['audits'];

    public function iati_code()
    {
        return iati_get_code_value('AidType', $this->code);
    }

    public function iati_vocabulary()
    {
        return iati_get_code_value('AidTypeVocabulary', $this->vocabulary);
    }
}
