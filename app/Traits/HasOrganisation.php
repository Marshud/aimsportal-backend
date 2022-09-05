<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

/**
 * Class UsedByTeams.
 */
trait HasOrganisation
{
    /**
     * Boot the global scope.
     */
    protected static function bootHasOrganisation()
    {
        static::addGlobalScope('organisation', function (Builder $builder) {

            if (app()->runningInConsole()) {
//                $this->scopeAllOrganisations($builder);
            } else {
                static::teamGuard();
                $builder->where($builder->getQuery()->from . '.organisation_id', auth()->user()->currentOrganisation->getKey());
            }

        });

        static::saving(function (Model $model) {
            if (!isset($model->organisation_id)) {
                static::teamGuard();

                $model->organisation_id = auth()->user()->currentOrganisation->getKey();
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

        if (auth()->guest() || !auth()->user()->currentOrganisation) {
            throw new Exception('No authenticated user with selected organisation present.');
        }

    }
}
