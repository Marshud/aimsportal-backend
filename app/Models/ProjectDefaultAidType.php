<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ProjectDefaultAidType extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $with = ['audits'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function iati_code()
    {
        $vocabulary = $this->iati_vocabulary();
        if ($vocabulary && $vocabulary->code == '1') {
            return iati_get_code_value('AidType', $this->code);
        }
        if ($vocabulary && $vocabulary->code == '2') {
            return iati_get_code_value('EarmarkingCategory', $this->code);
        }

        if ($vocabulary && $vocabulary->code == '4') {
            return iati_get_code_value('CashandVoucherModalities', $this->code);
        }
        
       return iati_get_code_value($vocabulary->name, $this->code);
    }

    public function iati_vocabulary()
    {
        return iati_get_code_value('AidTypeVocabulary', $this->vocabulary);
    }
}
