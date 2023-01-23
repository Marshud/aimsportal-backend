<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTransactionAidType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function iati_code()
    {
        return iati_get_code_value('AidType', $this->code);
    }

    public function iati_vocabulary()
    {
        return iati_get_code_value('AidTypeVocabulary', $this->vocabulary);
    }
}
