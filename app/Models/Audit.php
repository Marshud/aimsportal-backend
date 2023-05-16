<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class Audit extends Model
{
    use HasFactory, Prunable;

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        $monthsToKeepLogs = get_system_setting('months_to_keep_project_changes') ?? 6;
        return static::where('created_at', '<=', now()->subMonth($monthsToKeepLogs));
    }
    
}
