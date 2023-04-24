<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTransactionSector extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['narratives'];

    public function transaction()
    {
        return $this->belongsTo(ProjectTransaction::class, 'project_transaction_id');
    }

    public function narratives()
    {
        return $this->morphMany(ProjectNarrative::class, 'element');
    }

    public function iati_code()
    {
        $vocabulary = $this->iati_vocabulary();
        if ($vocabulary && $vocabulary->code == '1') {
            return iati_get_code_value('Sector', $this->code);
        }

        if ($vocabulary && $vocabulary->code == '2') {
            return iati_get_code_value('SectorCategory', $this->code);
        }

        if ($vocabulary && $vocabulary->code == '7') {
            return iati_get_code_value('UNSDG-Goals', $this->code);
        }

        if ($vocabulary && $vocabulary->code == '8') {
            return iati_get_code_value('UNSDG-Targets', $this->code);
        }
        
       return iati_get_code_value($vocabulary->name, $this->code);
    }

    public function iati_vocabulary()
    {
        return iati_get_code_value('SectorVocabulary', $this->vocabulary);
    }
}
