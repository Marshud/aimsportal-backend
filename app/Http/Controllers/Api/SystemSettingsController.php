<?php

namespace App\Http\Controllers\Api;

use App\Enums\CoreRoles;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrganisationCategoryResource;
use App\Http\Resources\OrganisationResource;
use App\Http\Resources\ProjectResource;
use App\Models\Organisation;
use App\Models\OrganisationCategory;
use App\Models\Project;
use App\Models\ProjectHumanitarianScope;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class SystemSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['verified.app', 'auth:sanctum', 'isapproved'])->except('index');
    }

    public function getSystemSettings(Request $request)
    {
        if (!$request->user()->hasRole(CoreRoles::SuperAdministrator->value))
        {
            return response()->error('Unauthorized', 403); 
        }
        
        $availableSettings = config('ss-aims.system_variables');

        $setSettings = array_map(function($setting) {
            return [
                'key' => $setting['name'],
                'options' => $setting['options'],
                'value' => get_system_setting($setting['name'], false),
            ];
        }, $availableSettings);
        

        return response()->success($setSettings);
    }

    public function storeSystemSettings(Request $request)
    {
        if (!$request->user()->hasRole(CoreRoles::SuperAdministrator->value))
        {
            return response()->error('Unauthorized', 403); 
        }
        $validator = Validator::make($request->all(),[
            'settings' => 'required|array',
            'settings.*.key' => ["required", Rule::in(array_column(config('ss-aims.system_variables'), 'name'))],
            'settings.*.value' => 'required',

        ]); 

        

        if ($validator->fails()) {
                
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

        $settings = $request->settings;
        foreach($settings as $setting) {
            set_system_setting($setting['key'], $setting['value']);
        }
        Cache::forget('system_settings');

        return response()->success(__('messages.success'));
    }

    



}