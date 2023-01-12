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

class ProjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum','isapproved'])->except('index');
    }

    public function store(Request $request)
    {
        if (!$request->user()->isAbleTo('create-projects'))
        {
            return response()->error('Unauthorized', 403); 
        }
        
        $validator = Validator::make($request->all(),[
            'project_title' => 'required|max:255',
            'sectors' => 'array',
            'budgets' => 'array|required',
            'participating_organisations' => 'array|required',
            'project_objective' => 'required|max:255',
            'project_planned_start_date' => 'date|required',
            'project_planned_end_date' => 'required|date',
            'project_actual_start_date' => 'nullable|date',
            'project_actual_end_date' => 'nullable|date',
            'organisation_id' => 'required|exists:organisations,id',
            'activity_scope' => 'nullable|numeric',
            //'is_iati_project' => 'boolean',

        ]);
        

        if ($validator->fails()) {
                
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

        $sectors = $request->sectors;
        $sector_percentage_total = array_sum(array_column($sectors, 'sector_percentage'));

        if ($sector_percentage_total != 100)
        {
            return response()->error(__('messages.invalid_request'), 422, 'sector percentage does not total 100');
        }

        $regions = $request->recipient_regions;
        $region_percentage_total = ($regions) ? array_sum(array_column($regions, 'region_percentage')) : null;

        if ($region_percentage_total && $region_percentage_total != 100)
        {
            return response()->error(__('messages.invalid_request'), 422, 'recipient region percentage does not total 100');
        }

        $isIati = $request->is_iati_project;

        DB::beginTransaction();
        //todo check for duplicate project by organisation, title, start and end date // or tune activity id if iati
        if (!$isIati)
        {
            $project = Project::create([
                'organisation_id' => $request->organisation_id,
                'title' => $request->project_title,
                'status' => $request->project_status ?? 'active',
                'capital_spend_percentage' => $request->project_capital_spend_percentage ?? 0,
                'conditions_attached' => $request->project_conditions ?? 0,
                'activity_scope' => $request->activity_scope ?? 4,
            ]);

            try {
                $project->title_translations()->create([
                    'narrative' => $request->project_title,
                    'lang' => $request->user()->language ?? 'en',
                ]);

                $project_description = $project->project_descriptions()->create([
                    'type' => 1,
                ]);

                $project_description->narratives()->create([
                    'narrative' => $request->project_objective,
                    'lang' => $request->user()->language ?? 'en',
                ]);

                $project_planned_start_date = $project->activity_dates()->create([
                    'type' => 1,
                    'iso_date' => $request->project_planned_start_date
                ]);

                $project_planned_start_date->narratives()->create([
                    'narrative' => 'Planned start date of project',
                    'lang' => 'en',
                ]);

                $project_planned_end_date = $project->activity_dates()->create([
                    'type' => 1,
                    'iso_date' => $request->project_planned_end_date
                ]);
                
                $project_planned_end_date->narratives()->create([
                    'narrative' => 'Planned end date of project',
                    'lang' => 'en',
                ]);

                if ($request->project_actual_start_date)
                {
                    $project_actual_start_date = $project->activity_dates()->create([
                        'type' => 1,
                        'iso_date' => $request->project_actual_start_date
                    ]);
                    
                    $project_actual_start_date->narratives()->create([
                        'narrative' => 'Actual start date of project',
                        'lang' => 'en',
                    ]);
                }

                if ($request->project_actual_end_date)
                {
                    $project_actual_end_date = $project->activity_dates()->create([
                        'type' => 1,
                        'iso_date' => $request->project_actual_end_date
                    ]);
                    
                    $project_actual_end_date->narratives()->create([
                        'narrative' => 'Actual end date of project',
                        'lang' => 'en',
                    ]);
                }
                
                foreach ($sectors as $sector) {
                    $project_sector = $project->sectors()->create([
                        'code' => $sector['sector_code'],
                        'percentage' => $sector['sector_percentage'],
                        'vocabulary' => $sector['sector_vocabulary'],
                        'vocabulary_uri' => $sector['sector_vocabulary_uri'] ?? null,
                    ]);
                    if (!empty($sector['sector_narrative'])) {
                        $sector_narrative = $sector['sector_narrative'] ?? [];
                        foreach($sector_narrative as $narrative) {
                            $project_sector->narratives()->create([
                                'narrative' => $narrative['narrative'],
                                'lang' => $narrative['lang'] ?? $request->user()->language ?? 'en',
                            ]);
                        }
                    }
                    
                }

                $recipient_country = $project->recipient_countries()->create([
                    'code' => $request->recipient_country ?? 'SS',
                    'percentage' => 100
                ]);

                if ($request->recipient_country_narrative) 
                {
                    $recipient_country->narratives()->create([
                        'narrative' => $request->recipient_country_narrative,
                        'lang' => $request->user()->language ?? 'en',
                    ]);
                }

                foreach ($regions as $region) {
                    $recipient_region = $project->recipient_regions()->create([
                        'code' => $region['region_code'],
                        'percentage' => $region['region_percentage'],
                        'vocabulary' => $region['region_vocabulary'],
                        'vocabulary_uri' => $region['region_vocabulary_uri'] ?? null,
                    ]);
                    if (!empty($region['region_narrative'])) {
                        $region_narrative = $region['region_narrative'] ?? [];
                        foreach($region_narrative as $narrative) {
                            $recipient_region->narratives()->create([
                                'narrative' => $narrative['narrative'],
                                'lang' => $narrative['lang'] ?? $request->user()->language ?? 'en',
                            ]);
                        }
                    }
                    
                }
                $budgets = $request->budgets;
                foreach($budgets as $budget) {
                    //validate and throw error to stop creation
                    $project->budgets()->create([
                        'type' => $budget['type'],
                        'status' => $budget['status'],
                        'period_start' => $budget['period_start'],
                        'period_end' => $budget['period_end'],
                        'value_currency' => $budget['value_currency'],
                        'value_date' => Carbon::parse($budget['value_date'])->format('Y-m-d'),
                        'value_amount' => $budget['value_amount']
                    ]);
                }

                $participating_organisations = $request->participating_organisations;
                foreach($participating_organisations as $organisation) {
                    //validate
                    $project->participating_organisations()->create([
                        'organisation_id' => $organisation['organisation_id'],
                        'type' => $organisation['type'],
                        'role' => $organisation['role'],
                    ]);
                }
                

                DB::commit();
            } catch(Throwable $e) {
                DB::rollBack();
                return response()->error(" ".$e->getMessage(), 500);
            }
        }
        
        return response()->success(new ProjectResource($project));
        
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