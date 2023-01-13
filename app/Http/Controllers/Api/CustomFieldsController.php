<?php

namespace App\Http\Controllers\Api;

use App\Enums\CoreRoles;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrganisationResource;
use App\Http\Resources\UserResource;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use App\Enums\FieldTypes;
use App\Http\Resources\CustomFieldResource;
use App\Models\CustomField;
use App\Models\Meta;
use Illuminate\Support\Facades\Auth;

class CustomFieldsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'isapproved'])->except('indexy');
    }

    public function store(Request $request)
    {
        if (!$request->user()->isAbleTo('create-custom-fields'))
        {
            return response()->error('Unauthorized', 403); 
        }
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:custom_fields',
            'field_type' => ['required', new Enum(FieldTypes::class)],
            'required' => 'nullable|boolean',
            'options' => 'nullable|array'
        ]);

        if ($validator->fails()) {
			
			return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

        $custom_field = new CustomField;
        $custom_field->name = $request->name;
        $custom_field->field_type = $request->field_type;
        $custom_field->required = $request->required ?? false;
        $custom_field->save();

        if ($request->has('options')) {
            foreach($request->options as $custom_field_option)
            {
                $custom_field->options()->create(['value' => $custom_field_option]);
            }
        }

        return response()->success(new CustomFieldResource($custom_field));

    }

    public function index(Request $request)
    {
        $custom_fields = CustomField::all();
       
        if ($request->user() && $request->user()->hasRole(CoreRoles::SuperAdministrator->value)) {            
            $custom_fields = CustomField::allOrganisations()->get();
        }
            
        
        
        return response()->success(CustomFieldResource::collection($custom_fields));
        
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('view-custom-fields'))
        {
            return response()->error('Unauthorized', 403); 
        }

        $custom_field = CustomField::find($id);

        if (!$custom_field) {
            return response()->error(__('messages.not_found'), 404);
        }

        return response()->success(new CustomFieldResource($custom_field));
    }

    public function update(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('update-custom-fields'))
        {
            return response()->error('Unauthorized', 403); 
        }
        $validator = Validator::make($request->all(),[
            'name' => "required|unique:custom_fields,name,$id",
            'field_type' => ['required', new Enum(FieldTypes::class)],
            'required' => 'nullable|boolean',
            'options' => 'nullable|array'
        ]);

        if ($validator->fails()) {
			
			return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

        $custom_field = CustomField::find($id);

        if (!$custom_field) {
            return response()->error(__('messages.not_found'), 404);
        }

        $custom_field->name = $request->name;
        $custom_field->field_type = $request->field_type;
        $custom_field->required = $request->required ?? false;
        $custom_field->save();

        if ($request->has('options')) {
            foreach($request->options as $custom_field_option)
            {
                $custom_field->options()->updateOrcreate(['value' => $custom_field_option]);
            }
        }

        return response()->success(new CustomFieldResource($custom_field));

    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('delete-custom-fields'))
        {
            return response()->error('Unauthorized', 403); 
        }
        $custom_field = CustomField::find($id);
        if (!$custom_field) 
        {
            return response()->error(__('messages.not_found'), 404);
        }
        $has_meta = Meta::where('key', $custom_field->name);
         if (!$has_meta->isEmpty())
         {
             return response()->error(__('messages.error_delete'), 400);
         }
        $custom_field->delete();
        return response()->success(['message' => __('messages.success_deleted')]);
    }

}