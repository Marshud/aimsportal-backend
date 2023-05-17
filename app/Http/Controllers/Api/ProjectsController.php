<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrganisationCategoryResource;
use App\Http\Resources\OrganisationResource;
use App\Http\Resources\ProjectResource;
use App\Models\Organisation;
use App\Models\OrganisationCategory;
use App\Models\Project;
use App\Models\ProjectBudget;
use App\Models\ProjectHumanitarianScope;
use App\Models\ProjectLocation;
use App\Models\ProjectParticipatingOrg;
use App\Models\ProjectRecipientRegion;
use App\Models\ProjectSector;
use App\Models\ProjectTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum','isapproved'])->except(['index', 'show']);
    }

    public function store(Request $request)
    {
        if (!$request->user()->isAbleTo('create-projects'))
        {
            return response()->error('Unauthorized', 403); 
        }
        
        $validator = Validator::make($request->all(),[
            'project_title' => 'required|max:255',
            'sectors' => 'array|required',
            'budgets' => 'array|required',
            'transactions' => 'array|required',
            'participating_organisations' => 'array|required',
            'recipient_countries' => 'array|required',
            'project_objective' => 'required',
            'project_planned_start_date' => 'date|required',
            'project_planned_end_date' => 'required|date',
            'project_actual_start_date' => 'nullable|date',
            'project_actual_end_date' => 'nullable|date',
            'organisation_id' => 'required|exists:organisations,id',
            'activity_scope' => 'nullable|numeric',
            'activity_status' => 'required|numeric',
            'locations' => 'nullable|array',
            'humanitarian' => 'required|boolean'
            //'is_iati_project' => 'boolean',

        ],[
            'transactions.required' => __('messages.invalid_transaction')
        ]);
        

        if ($validator->fails()) {
                
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

        $sectors = $request->sectors;

        if (!$this->doPercentagesMatch($sectors, 'sector_vocabulary', 'sector_percentage'))
        {
            return response()->error(__('messages.invalid_request'), 422, 'some of the sector vocabularies percentage does not total 100');
        }

        $regions = $request->recipient_regions;

        if (!$this->doPercentagesMatch($regions, 'region_vocabulary', 'region_percentage'))
        {
            return response()->error(__('messages.invalid_request'), 422, 'some of the recipient region vocabularies percentage does not total 100');
        }

        $isIati = $request->is_iati_project;

        if (!can_create_project(Organisation::find($request->organisation_id)))
        {
            return response()->error('Unauthorized', 403); 
        }

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
                'activity_status' => $request->activity_status,
                'humanitarian' => $request->humanitarian,
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
                    'type' => 3,
                    'iso_date' => $request->project_planned_end_date
                ]);
                
                $project_planned_end_date->narratives()->create([
                    'narrative' => 'Planned end date of project',
                    'lang' => 'en',
                ]);

                if ($request->project_actual_start_date)
                {
                    $project_actual_start_date = $project->activity_dates()->create([
                        'type' => 2,
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
                        'type' => 4,
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

                $recipient_countries = $request->recipient_countries;

                foreach($recipient_countries as $country) {

                    $recipient_country = $project->recipient_countries()->create([
                        'code' => $country['country_code'] ?? 'SS',
                        'percentage' => $country['country_percentage'] ?? 100
                    ]);

                    if (!empty($country['narratives'])) 
                    {
                        $country_narratives = $country['narratives'];

                        foreach($country_narratives as $narrative) {
                            $recipient_country->narratives()->create([
                                'narrative' => $narrative,
                                'lang' => $narrative['lang'] ?? $request->user()->language ?? 'en',
                            ]);
                        }
                        
                    }
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
                        'value_amount' => $budget['value_amount'],
                        'ssp_value_amount' => $budget['value_amount_ssp'] ?? null,
                        'usd_value_amount' => $budget['value_amount_usd'] ?? null,
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

                $transactions = $request->transactions;
                foreach ($transactions as $transaction) {
                    $thisTransaction = $project->transactions()->create([
                        "ref" => $transaction['ref'] ?? '',
                        "humanitarian" => $transaction['humanitratian'] ?? 0,
                        "transaction_type_code" => $transaction['transaction_type_code'],
                        "transaction_date" => $transaction['transaction_date'],
                        "value_currency" => $transaction['value_currency'],
                        "value_date" => $transaction['value_date'],
                        "value_amount" => $transaction['value_amount'],
                        'ssp_value_amount' => $transaction['value_amount_ssp'] ?? null,
                        'usd_value_amount' => $transaction['value_amount_usd'] ?? null,
                        "disbursement_channel_code" => $transaction['disbursement_channel_code'],
                        "recipient_country_code" => $transaction['recipient_country_code'],
                        "recipient_region_code" => $transaction['recipient_region_code'],
                        "recipient_region_vocabulary" => $transaction['recipient_region_vocabulary'],
                        "flow_type_code" => $transaction['flow_type_code'],
                        "finance_type_code" => $transaction['finance_type_code'],
                        "tied_status_code" => $transaction['tied_status_code'],
                    ]);

                    if (!empty($transaction['sectors'])) {
                        $sectors = $transaction['sectors'];
                        foreach ($sectors as $sector) {
                            $transactionSector = $thisTransaction->sectors()->create([
                                'vocabulary' => $sector['sector_vocabulary'],
                                'vocabulary_uri' => $sector['sector_vocabulary_uri'] ?? null,
                                'code' => $sector['sector_code'],
                            ]);

                            if (!empty($sector['sector_narrative'])) {
                                $sector_narrative = $sector['sector_narrative'] ?? [];
                                foreach($sector_narrative as $narrative) {
                                    $transactionSector->narratives()->create([
                                        'narrative' => $narrative['narrative'],
                                        'lang' => $narrative['lang'] ?? $request->user()->language ?? 'en',
                                    ]);
                                }
                            }
                        }
                        
                    }
                    if (!empty($transaction['provider_org'])) {

                        $provider_org = $transaction['provider_org'];
                        $thisProviderOrg = $thisTransaction->provider_org()->create([
                            'organisation_id' => $provider_org['organisation_id'],
                            'type' => $provider_org['type'],
                            'ref' => $provider_org['ref'] ?? null,
                        ]);

                        if (!empty($provider_org['narrative'])) {
                            $thisProviderOrg->narratives()->create([
                                'narrative' => $provider_org['narrative'],
                                'lang' => $provider_org['lang'] ?? $request->user()->language ?? 'en',
                            ]);
                            
                        }
                    }

                    if (!empty($transaction['receiver_org'])) {

                        $receiver_org = $transaction['receiver_org'];
                        $thisReceiverOrg = $thisTransaction->receiver_org()->create([
                            'organisation_id' => $receiver_org['organisation_id'],
                            'type' => $receiver_org['type'],
                            'ref' => $receiver_org['ref'] ?? null,
                        ]);

                        if (!empty($receiver_org['narrative'])) {
                            $thisReceiverOrg->narratives()->create([
                                'narrative' => $receiver_org['narrative'],
                                'lang' => $receiver_org['lang'] ?? $request->user()->language ?? 'en',
                            ]);
                            
                        }
                    }

                    if (!empty($transaction['aid_types'])) {

                        foreach($transaction['aid_types'] as $aidType) {
                            $thisTransaction->aid_types()->create([
                                'code' => $aidType['code'],
                                'vocabulary' => $aidType['vocabulary']
                            ]);
                        }
                        
                    }
                }

                $locations = $request->locations;
                foreach($locations as $location) {
                    $ref = isset($location['state']) ? 'state-id:'.$location['state'] : null;
                    $project->locations()->create([
                        'state_id' => $location['state'] ?? null,
                        'county_id' => $location['county'] ?? null,
                        'payam_id' => $location['payam'] ?? null,
                        'ref' => $ref,
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
        if (!$request->user()->isAbleTo('update-projects'))
        {
            return response()->error('Unauthorized', 403); 
        }

        $validator = Validator::make($request->all(),[
            'project_title' => 'required|max:255',
            'sectors' => 'array|required',
            'budgets' => 'array|required',
            'transactions' => 'array|required',
            'participating_organisations' => 'array|required',
            'recipient_countries' => 'array|required',
            'project_objective' => 'required',
            'project_planned_start_date' => 'date|required',
            'project_planned_end_date' => 'required|date',
            'project_actual_start_date' => 'nullable|date',
            'project_actual_end_date' => 'nullable|date',
            'organisation_id' => 'required|exists:organisations,id',
            'activity_scope' => 'nullable|numeric',
            'activity_status' => 'required|numeric',
            'locations' => 'nullable|array',
            'humanitarian' => 'required|boolean'
            //'is_iati_project' => 'boolean',

        ]);
        

        if ($validator->fails()) {
                
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

        $sectors = $request->sectors;

        if (!$this->doPercentagesMatch($sectors, 'sector_vocabulary', 'sector_percentage'))
        {
            return response()->error(__('messages.invalid_request'), 422, 'some of the sector vocabularies percentage does not total 100');
        }

        $regions = $request->recipient_regions;

        if (!$this->doPercentagesMatch($regions, 'region_vocabulary', 'region_percentage'))
        {
            return response()->error(__('messages.invalid_request'), 422, 'recipient region percentage does not total 100');
        }

        $isIati = $request->is_iati_project;

        
        //todo check for duplicate project by organisation, title, start and end date // or tune activity id if iati
        if (!$isIati)
        {
            $project = Project::findOrfail($id);

            if (!can_edit_project($project)) {
                return response()->error('Unauthorized', 403); 
            }

            DB::beginTransaction();
            $project->update([
                'organisation_id' => $request->organisation_id,
                'title' => $request->project_title,
                'status' => $request->project_status ?? 'active',
                'capital_spend_percentage' => $request->project_capital_spend_percentage ?? 0,
                'conditions_attached' => $request->project_conditions ?? 0,
                'activity_scope' => $request->activity_scope ?? 4,
                'activity_status' => $request->activity_status,
                'humanitarian' => $request->humanitarian,
            ]);

            try {
                $project->title_translations()->updateOrCreate([
                    'narrative' => $request->project_title,
                    'lang' => $request->user()->language ?? 'en',
                ]);

                $project_description = $project->project_descriptions()->create([
                    'type' => 1, 
                ]);

                $project_description->narratives()->updateOrCreate([
                    'narrative' => $request->project_objective,
                    'lang' => $request->user()->language ?? 'en',
                ]);

                $project_planned_start_date = $project->activity_dates()->updateOrCreate(
                    [
                        'type' => 1
                    ],
                    [
                        'iso_date' => $request->project_planned_start_date
                    ]
                );

                $project_planned_start_date->narratives()->updateOrCreate([
                    'narrative' => 'Planned start date of project',
                    'lang' => 'en',
                ]);

                $project_planned_end_date = $project->activity_dates()->updateOrCreate([
                    'type' => 3],[
                    'iso_date' => $request->project_planned_end_date
                ]);
                
                $project_planned_end_date->narratives()->updateOrCreate([
                    'narrative' => 'Planned end date of project',
                    'lang' => 'en',
                ]);

                if ($request->project_actual_start_date)
                {
                    $project_actual_start_date = $project->activity_dates()->updateOrCreate([
                        'type' => 2],[
                        'iso_date' => $request->project_actual_start_date
                    ]);
                    
                    $project_actual_start_date->narratives()->updateOrCreate([
                        'narrative' => 'Actual start date of project',
                        'lang' => 'en',
                    ]);
                }

                if ($request->project_actual_end_date)
                {
                    $project_actual_end_date = $project->activity_dates()->updateOrCreate([
                        'type' => 4],[
                        'iso_date' => $request->project_actual_end_date
                    ]);
                    
                    $project_actual_end_date->narratives()->updateOrCreate([
                        'narrative' => 'Actual end date of project',
                        'lang' => 'en',
                    ]);
                }
                
                foreach ($sectors as $sector) {
                    $project_sector = $project->sectors()->updateOrCreate(
                        [
                            'code' => $sector['sector_code'],
                            'vocabulary' => $sector['sector_vocabulary'], 
                        ],
                        [                        
                        'percentage' => $sector['sector_percentage'],                        
                        'vocabulary_uri' => $sector['sector_vocabulary_uri'] ?? null,
                        ]
                    );
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
               

                $recipient_countries = $request->recipient_countries;

                foreach($recipient_countries as $country) {                    

                    $recipient_country = $project->recipient_countries()->updateOrCreate(
                        [
                        'code' => $country['country_code'] ?? 'SS'
                        ],
                        [
                        'percentage' => $country['country_percentage'] ?? 100
                        ]
                    );

                    if (!empty($country['narratives'])) 
                    {
                        $country_narratives = $country['narratives'];

                        foreach($country_narratives as $narrative) {
                            $recipient_country->narratives()->updateOrCreate([
                                'narrative' => $narrative['narrative'],
                                'lang' => $narrative['lang'] ?? 'en',
                            ]);
                        }
                        
                    }
                }

                foreach ($regions as $region) {
                    $recipient_region = $project->recipient_regions()->updateOrCreate(
                        [
                        'code' => $region['region_code'] ],
                        [
                        'vocabulary' => $region['region_vocabulary'],
                        'percentage' => $region['region_percentage'],                        
                        'vocabulary_uri' => $region['region_vocabulary_uri'] ?? null,
                        ]
                    );
                    if (!empty($region['region_narrative'])) {
                        $region_narrative = $region['region_narrative'] ?? [];
                        foreach($region_narrative as $narrative) {
                            $recipient_region->narratives()->updateOrCreate([
                                'narrative' => $narrative['narrative'],
                                'lang' => $narrative['lang'] ?? $request->user()->language ?? 'en',
                            ]);
                        }
                    }
                    
                }
                $budgets = $request->budgets;
                foreach($budgets as $budget) {
                    //validate and throw error to stop creation
                    $project->budgets()->updateOrCreate(
                        [
                            'type' => $budget['type'],
                            'status' => $budget['status'],
                            'period_start' => $budget['period_start'],
                            'period_end' => $budget['period_end']
                        ],
                        [
                        'value_currency' => $budget['value_currency'],
                        'value_date' => Carbon::parse($budget['value_date'])->format('Y-m-d'),
                        'value_amount' => $budget['value_amount']
                        ]
                    );
                }

                $participating_organisations = $request->participating_organisations;
                foreach($participating_organisations as $organisation) {
                    //validate
                    $project->participating_organisations()->updateOrCreate(
                        [
                            'organisation_id' => $organisation['organisation_id']
                        ],[
                            'type' => $organisation['type'],
                            'role' => $organisation['role'],
                        ]
                    );
                }

                $transactions = $request->transactions;
                foreach ($transactions as $transaction) {
                    $thisTransaction = $project->transactions()->updateOrCreate([
                        "ref" => $transaction['ref'] ?? '',
                        "transaction_type_code" => $transaction['transaction_type_code'],
                        "disbursement_channel_code" => $transaction['disbursement_channel_code'],
                        "value_amount" => $transaction['value_amount']
                    ],[
                        "humanitarian" => $transaction['humanitratian'] ?? 0,
                        "transaction_date" => $transaction['transaction_date'],
                        "value_currency" => $transaction['value_currency'],
                        "value_date" => $transaction['value_date'],
                        "recipient_country_code" => $transaction['recipient_country_code'],
                        "recipient_region_code" => $transaction['recipient_region_code'],
                        "recipient_region_vocabulary" => $transaction['recipient_region_vocabulary'],
                        "flow_type_code" => $transaction['flow_type_code'],
                        "finance_type_code" => $transaction['finance_type_code'],
                        "tied_status_code" => $transaction['tied_status_code'],
                    ]);

                    if (!empty($transaction['sectors'])) {
                        $sectors = $transaction['sectors'];
                        foreach ($sectors as $sector) {
                            $transactionSector = $thisTransaction->sectors()->updateOrCreate([
                                'vocabulary' => $sector['sector_vocabulary'],
                                'code' => $sector['sector_code']
                            ],[
                                'vocabulary_uri' => $sector['sector_vocabulary_uri'] ?? null,
                                
                            ]);

                            if (!empty($sector['sector_narrative'])) {
                                $sector_narrative = $sector['sector_narrative'] ?? [];
                                foreach($sector_narrative as $narrative) {
                                    $transactionSector->narratives()->updateOrcreate([
                                        'narrative' => $narrative['narrative'],
                                        'lang' => $narrative['lang'] ?? $request->user()->language ?? 'en',
                                    ]);
                                }
                            }
                        }
                        
                     }
                    if (!empty($transaction['provider_org'])) {

                        $provider_org = $transaction['provider_org'];
                        $thisProviderOrg = $thisTransaction->provider_org()->updateOrCreate([
                            'organisation_id' => $provider_org['organisation_id']
                        ],[
                            'type' => $provider_org['type'],
                            'ref' => $provider_org['ref'] ?? null,
                        ]);

                        if (!empty($provider_org['narrative'])) {
                            $thisProviderOrg->narratives()->updateOrCreate([
                                'narrative' => $provider_org['narrative'],
                                'lang' => $provider_org['lang'] ?? $request->user()->language ?? 'en',
                            ]);
                            
                        }
                    }

                    if (!empty($transaction['receiver_org'])) {

                        $receiver_org = $transaction['receiver_org'];
                        $thisReceiverOrg = $thisTransaction->receiver_org()->updateOrCreate([
                            'organisation_id' => $receiver_org['organisation_id']
                        ],[
                            'type' => $receiver_org['type'],
                            'ref' => $receiver_org['ref'] ?? null,
                        ]);

                        if (!empty($receiver_org['narrative'])) {
                            $thisReceiverOrg->narratives()->updateOrCreate([
                                'narrative' => $receiver_org['narrative'],
                                'lang' => $receiver_org['lang'] ?? $request->user()->language ?? 'en',
                            ]);
                            
                        }
                    }

                    if (isset($transaction['aid_types']) && !empty($transaction['aid_types'])) {
                        
                        foreach($transaction['aid_types'] as $aidType) {
                            if (count($aidType) > 0) {
                                $thisTransaction->aid_types()->updateOrCreate([
                                    'code' => $aidType['code'],
                                    'vocabulary' => $aidType['vocabulary']
                                ]);
                            }
                        }
                        
                    }
                }

                $locations = $request->locations;
                foreach($locations as $location) {
                    $ref = isset($location['state']) ? 'state-id:'.$location['state'] : null;
                    ProjectLocation::updateOrCreate(
                        [
                            'project_id' => $project->getKey(),
                            'ref' => $ref
                        ],
                        [
                            'state_id' => $location['state'] ?? null,
                            'county_id' => $location['county'] ?? null,
                            
                        ]
                    );
                }
                

                DB::commit();
            } catch(Throwable $e) {
                DB::rollBack();
                Log::error(['ERROR_SAVING_PROJECT' => $e->getMessage()]);
                return response()->error(" ".$e->getMessage(), 500);
            }
        }
        
        return response()->success(new ProjectResource($project));
    }

    public function show(Request $request, $id)
    {
        // if (!$request->user()->isAbleTo('view-projects'))
        // {
        //     return response()->error('Unauthorized', 403); 
        // }
        $project = Project::find($id);
        if (!$project) {
            return response()->error(__('messages.not_found'), 404);
        }

        return response()->success(new ProjectResource($project));
    }

    public function index(Request $request)
    {
       
       $builder = Project::query();
       $builder->where(function ($q) {
            if (request()->has('organisation') && request()->filled('organisation')) {
                $q->whereHas('participating_organisations', function ($q) {
                    $q->where('organisation_id', request()->get('organisation'));
                });
            }

            if (request()->has('title') && request()->filled('title')) {
                $q->where('title', 'like', '%' . request()->get('title') . '%');
            }
            
            if (request()->has('state') && request()->filled('state')) {
                $q->whereHas('locations', function ($q) {
                    if(request()->filled('payam')) {
                        $q->where('payam_id', request()->get('payam'));
                    }

                    else if(request()->filled('county')) {
                        $q->where('county_id', request()->get('county'));
                    }

                    else {
                        $q->where('state_id', request()->get('state'));
                    }
                    
                });
            }

            if (request()->has('parent_sector') && request()->filled('parent_sector')) {
                $q->whereHas('sectors', function ($q) {
                    if(request()->filled('sector')) {
                        $q->where('vocabulary', request()->get('parent_sector'))
                            ->where('code', request()->get('sector'));
                    }
                    

                    else {
                        $q->where('vocabulary', request()->get('parent_sector'));
                    }
                    
                });
            }
            if (request()->has('start_date') && request()->filled('start_date')) {
                $q->whereHas('activity_dates', function ($q) {
                    if(request()->filled('end_date')) {
                        $q->whereBetween('iso_date', [request()->get('start_date'), request()->get('end_date')]);
                    }

                    else {
                        $q->where('iso_date', '>=', request()->get('start_date'))
                            ->where('type', 1);
                    }
                    
                });
            }
            
        });

    

        return ProjectResource::collection($builder->paginate(20));
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('delete-projects'))
        {
            return response()->error('Unauthorized', 403); 
        }

        $project = Project::find($id);
        if (!$project) {
            return response()->error(__('messages.not_found'), 404);
        } 

        $project->delete();

        return response()->success(__('messages.success_deleted'));
    }

    public function deleteParticipatingOrg(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('delete-projects'))
        {
            return response()->error('Unauthorized', 403); 
        }

        $participating_org = ProjectParticipatingOrg::find($id);
        if (!$participating_org)
        {
            return response()->error(__('messages.not_found'), 404);
        }

        $participating_org->delete();

        return response()->success(__('messages.success_deleted'));
    }

    public function deleteProjectBudget(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('delete-projects'))
        {
            return response()->error('Unauthorized', 403); 
        }

        $project_budget = ProjectBudget::find($id);
        if (!$project_budget)
        {
            return response()->error(__('messages.not_found'), 404);
        }

        $project_budget->delete();

        return response()->success(__('messages.success_deleted'));
    }

    public function deleteProjectSector(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('delete-projects'))
        {
            return response()->error('Unauthorized', 403); 
        }

        $project_sector = ProjectSector::find($id);
        if (!$project_sector)
        {
            return response()->error(__('messages.not_found'), 404);
        }

        $project_sector->delete();

        return response()->success(__('messages.success_deleted'));
    }

    public function deleteRecipientRegion(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('delete-projects'))
        {
            return response()->error('Unauthorized', 403); 
        }

        $project_recipient_region = ProjectRecipientRegion::find($id);
        if (!$project_recipient_region)
        {
            return response()->error(__('messages.not_found'), 404);
        }

        $project_recipient_region->delete();

        return response()->success(__('messages.success_deleted'));
    }

    public function deleteTransaction(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('delete-projects'))
        {
            return response()->error('Unauthorized', 403); 
        }

        $project_transaction = ProjectTransaction::find($id);
        if (!$project_transaction)
        {
            return response()->error(__('messages.not_found'), 404);
        }

        $project_transaction->sectors()->delete();
        $project_transaction->provider_org()->delete();
        $project_transaction->receiver_org()->delete();
        $project_transaction->delete();

        return response()->success(__('messages.success_deleted'));
    }

    public function deleteLocation(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('delete-projects'))
        {
            return response()->error('Unauthorized', 403); 
        }

        $project_location = ProjectLocation::find($id);
        if (!$project_location)
        {
            return response()->error(__('messages.not_found'), 404);
        }

        $project_location->delete();

        return response()->success(__('messages.success_deleted'));
    }

    private function doPercentagesMatch(array $array, string $id_column_name, $percentage_column_name) :bool
    {
        if (empty($array)) return true;

        $result = array_reduce($array, function($carry, $item) use($id_column_name, $percentage_column_name) { 
            if(!isset($carry[$item[$id_column_name]])){ 
                $carry[$item[$id_column_name]] = [$id_column_name =>$item[$id_column_name],$percentage_column_name=>$item[$percentage_column_name]]; 
            } else { 
                $carry[$item[$id_column_name]][$percentage_column_name] += $item[$percentage_column_name]; 
            } 
            return $carry; 
        });
        
        $max = array_search(max(array_column($result, $percentage_column_name)), array_column($result, $percentage_column_name));
        return $max < 100;
    }

}