<?php

namespace App\Http\Controllers\Api;

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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class IatiHelperController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum','isapproved'])->only('index');
    }

    public function getCodeListOptions(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'codelist' => 'required|string',
            'language' => 'nullable|exists:languages,code',

        ]); 

        if ($validator->fails()) {
                
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

        return iati_get_code_options($request->codelist, $request->language ?? 'en');
    }

    public function getCodelistValue(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'codelist' => 'required|string',
            'language' => 'nullable|exists:languages,code',

        ]); 

        if ($validator->fails()) {
                
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

        return iati_get_code_value($request->codelist, $request->code, $request->language ?? 'en');
    }

}