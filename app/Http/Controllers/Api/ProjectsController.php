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
use Illuminate\Support\Facades\Validator;

class ProjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum','isapproved'])->except('index');
    }

    public function store(Request $request)
    {
        if (!$request->user()->isAbleTo('create-organisation-categories'))
        {
            return response()->error('Unauthorized', 403); 
        }
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:organisation_categories',
        ]);

        if ($validator->fails()) {
			
			return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

        $category = new OrganisationCategory;
        $category->name = $request->name;
        $category->save();

        return response()->success(new OrganisationCategoryResource($category));
    }

    public function update(Request $request,$id)
    {
        if (!$request->user()->isAbleTo('update-organisation-categories'))
        {
            return response()->error('Unauthorized', 403); 
        }
        $category = OrganisationCategory::find($id);
        if (!$category)
        {
            return response()->error(__('messages.not_found'), 404);
        }
        $validator = Validator::make($request->all(),[
            'name' => "required|unique:organisation_categories,name,$id",
        ]);

        if ($validator->fails()) {
			
			return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }
        
        $category->name = $request->name;
        $category->save();

        return response()->success(new OrganisationCategoryResource($category));
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('view-organisation-categories'))
        {
            return response()->error('Unauthorized', 403); 
        }
        $category = OrganisationCategory::find($id);
        if (!$category) {
            return response()->error(__('messages.not_found'), 404);
        }

        return response()->success(new OrganisationCategoryResource($category));
    }

    public function index(Request $request)
    {
       // $project = Project::first();
        return response()->success(ProjectResource::collection(Project::all()));
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('delete-organisation-categories'))
        {
            return response()->error('Unauthorized', 403); 
        }

        $category = OrganisationCategory::find($id);
        if (!$category) {
            return response()->error(__('messages.not_found'), 404);
        }

        if ($category->organisations()->exists()) {
            return response()->error(__('messages.error_delete'), 400);
        }

        $category->delete();

        return response()->success(__('messages.success_deleted'));
    }

}