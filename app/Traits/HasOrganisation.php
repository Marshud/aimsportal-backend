<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\Guard;

/**
 * Class UsedByTeams.
 */
trait HasOrganisation
{
    private $auth;
    /**
     * Boot the global scope.
     */
    protected static function bootHasOrganisation()
    {        
       
        static::addGlobalScope('organisation', function (Builder $builder) {
            
            if (app()->runningInConsole()) {
//                $this->scopeAllOrganisations($builder);
            } else {
                
                if (auth('sanctum')->check()) {
                    Log::info(['TEAMGUARD' => auth('sanctum')->user()->id]);
                    $builder->where($builder->getQuery()->from . '.organisation_id', auth('sanctum')->user()->currentOrganisation->getKey());
                 }
                 else {
                     $builder->where($builder->getQuery()->from . '.organisation_id', 0);
                 }
            }

        });

        static::saving(function (Model $model) {
            if (!isset($model->organisation_id)) {
                if (auth('sanctum')->check()) {
                    $model->organisation_id = (auth('sanctum')->user()->currentOrganisation) ? auth('sanctum')->user()->currentOrganisation->getKey() : null;
                }
            }
        });

        
    }

    /**
     * @param Builder $query
     * @return mixed
     */
    public function scopeAllOrganisations(Builder $query)
    {
        return $query->withoutGlobalScope('organisation');
    }

    /**
     * @return mixed
     */
    public function organisation()
    {
        return $this->belongsTo(Config::get('laratrust.models.team'));
    }

    /**
     * @throws Exception
     */
    protected static function teamGuard()
    {

        if (auth()->guest() || !auth()->user()->current_organisation_id) {
          //  Log::info(['TEAMGUARD' => Auth::guard('web')->user()]);
            throw new Exception('No authenticated user with selected organisation present.');
        }

    }

    protected static function hasTeam(): bool
    {

        if (auth()->guest() || !auth()->user()->current_organisation_id) {
            return false;
        }

        return true;

    }
}

