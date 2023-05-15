<?php

use App\Http\Resources\CodelistTranslationResource;
use App\Models\Codelist;
use App\Models\CodelistOption;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Enums\CoreRoles;
use App\Models\Organisation;
use App\Models\Project;
use Illuminate\Support\Facades\Log;

if (!function_exists('iati_get_code_options'))
{
    function iati_get_code_options(string $codeName, string $language='en') : mixed
    {
        $codelist = Codelist::where('name', $codeName)->first();
        if ($codelist) {
            if ($language != 'en') {
                return transform_translation($codelist->translations->where('lang', $language));
            }
            return $codelist->options;
        }
        return collect([]);
    }
}

if (!function_exists('iati_get_code_value'))
{
    function iati_get_code_value(?string $codeName, ?string $codeValue, $language='en') : mixed
    {
        if (null === $codeName || $codeValue === null) return null;
        return iati_get_code_options($codeName, $language)->where('code', $codeValue)->first(); 
    }
}

function transform_translation(Collection $translations)
{
   $modified = $translations->map(function ($item) {
        return [
            'id' => $item->id,
            'code' => $item->codelist_option->code,
            'name' => $item->name,
            'description' => $item->description
        ];
        
    });

    return $modified;
}

if (!function_exists('get_system_setting')) {
    function get_system_setting(string $settingKey, bool $cached = true) : ?string
    {
        $systemSettings = ($cached) ? 
            Cache::remember('system_settings',now()->addHours(2), function () {
                return SystemSetting::all();
            }) : SystemSetting::all();
        if($systemSettings->isEmpty()) return null;
        
        return $systemSettings->where('key', $settingKey)->first()->value ?? null;
    }
}

if (!function_exists('set_system_setting')) {
    function set_system_setting(string $settingKey, string $settingValue)
    {
        SystemSetting::updateOrCreate(['key' => $settingKey], ['value' => $settingValue]);
        
    }
}

if (!function_exists('can_create_project')) {
    function can_create_project(Organisation $organisation) : bool
    {
        if (false === auth('sanctum')->check()) {
            return false;
        }
        if (auth('sanctum')->user()->hasRole(CoreRoles::SuperAdministrator->value)) {
            return true;
        }
        if (auth('sanctum')->user()->hasRole(CoreRoles::Manager->value) || auth('sanctum')->user()->hasRole(CoreRoles::Contributor->value)) {
            if(auth('sanctum')->user()->current_organisation_id == $organisation->id) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('can_edit_project')) {
    function can_edit_project($project) : bool
    {
        if (false === auth('sanctum')->check()) {
            return false;
        }
        if (auth('sanctum')->user()->hasRole(CoreRoles::SuperAdministrator->value)) {
            return true;
        }
        if (auth('sanctum')->user()->hasRole(CoreRoles::Manager->value) || auth('sanctum')->user()->hasRole(CoreRoles::Contributor->value)) {
            if(auth('sanctum')->user()->current_organisation_id == $project->organisation_id || in_array(auth('sanctum')->user()->current_organisation_id, $project->participating_organisations->pluck('organisation_id')->toArray())) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('can_see_audits')) {
    function can_see_audits($model = null) : bool
    {
        if (false === auth('sanctum')->check()) {
            return false;
        }

        if (auth('sanctum')->user()->hasRole(CoreRoles::SuperAdministrator->value)) {
            return true;
        }

        if (auth('sanctum')->user()->hasRole(CoreRoles::Manager->value)) {

            // match this organisation with logged in user organisation
            if ($model) {
                if (method_exists($model, 'project')) {
                    if(auth('sanctum')->user()->current_organisation_id == $model->project->organisation_id || in_array(auth('sanctum')->user()->current_organisation_id, $model->project->participating_organisations->pluck('organisation_id')->toArray())) {
                        return true;
                    }
                }
                if (method_exists($model, 'participating_organisations')) {
                    if(auth('sanctum')->user()->current_organisation_id == $model->organisation_id || in_array(auth('sanctum')->user()->current_organisation_id, $model->participating_organisations->pluck('organisation_id')->toArray())) {
                        return true;
                    }
                }
                
            }
            
        }

        return false;
    }
}




