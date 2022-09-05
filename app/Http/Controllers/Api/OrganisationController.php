<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrganisationResource;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganisationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum','isapproved'])->except('index');
    }

    public function store(Request $request)
    {
        if (!$request->user()->isAbleTo('create-organisations'))
        {
            return response()->error('Unauthorized',403); 
        }
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:organisations',
            'acronym' => 'required|string|size:3',
            'contact_person_name' => 'required|string|max:200',
            'contact_person_email' => 'required|email|max:200',
            'category' => 'required|exists:organisation_categories,id',
            'approved' => 'nullable|boolean',
            'address' => 'required',
            'long_name' => 'nullable'
        ]);

        if ($validator->fails()) {
			
			return response()->error(__('messages.invalid_request'),422,$validator->messages()->toArray());
        }
        
        $organisation = new Organisation;
        $organisation->name = $request->name;
        $organisation->acronym = $request->acronym;
        $organisation->category_id = $request->category;
        $organisation->contact_person_name = $request->contact_person_name;
        $organisation->contact_person_email = $request->contact_person_email;
        $organisation->address = $request->address;
        $organisation->approved = $request->approved ?? false;
        $organisation->description = $request->long_name ?? null;
        $organisation->save();

        return response()->success(new OrganisationResource($organisation));
        
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('view-organisations'))
        {
            return response()->error('Unauthorized',403); 
        }

        $organisation = Organisation::find($id);

        if (!$organisation) {
            return response()->error(__('messages.not_found'),404);
        }

        return response()->success(new OrganisationResource($organisation));
    }

    public function update(Request $request,$id)
    {
        if (!$request->user()->isAbleTo('update-organisations'))
        {
            return response()->error('Unauthorized',403); 
        }
        $organisation = Organisation::find($id);
        if (!$organisation) 
        {
            return response()->error(__('messages.not_found'),404);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:organisations',
            'accronym' => 'required|string|size:3',
            'contact_person_name' => 'required|string|size:200',
            'contact_person_email' => 'required|email|size:200',
            'category' => 'required|exists:organisation_categories,id',
            'approved' => 'requred|boolean',
            'long_name' => 'nullable'
        ]);
        
        if ($validator->fails()) {
			
			return response()->error(__('messages.invalid_request'),422,$validator->messages()->toArray());
        }

        $organisation->name = $request->name;
        $organisation->acronym = $request->acronym;
        $organisation->category_id = $request->category;
        $organisation->contact_person_name = $request->contact_person_name;
        $organisation->contact_person_email = $request->contact_person_email;
        $organisation->address = $request->address;
        $organisation->approved = $request->approved;
        $organisation->description = $request->long_name ?? null;
        $organisation->save();

        return response()->success(new OrganisationResource($organisation));

    }


    public function destroy(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('delete-organisations'))
        {
            return response()->error('Unauthorized',403); 
        }
        $organisation = Organisation::find($id);
        if (!$organisation) 
        {
            return response()->error(__('messages.not_found'),404);
        }

        if ($organisation->hasData())
        {
            $organisation->delete();
            return response()->success(['message' => __('messages.success_deleted')]);
        }
    }

    public function index(Request $request)
    {
        //list all for now but enable search and pagination later
        return response()->success(OrganisationResource::collection(Organisation::all()));
    }
}