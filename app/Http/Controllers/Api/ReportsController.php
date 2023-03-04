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
use App\Models\ProjectTransaction;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum','isapproved'])->only('index');
    }

    public function reportOnFundingtrends(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'currency' => 'nullable|string',
            'number_of_years' => 'nullable|numeric'

        ]); 
        $currency = $request->currency ?? 'USD';
        $no_of_years = $request->number_of_years ?? 12;
        if ($validator->fails()) {
                
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

       $report = DB::table('projects')
            ->join('project_transactions', 'projects.id', '=', 'project_transactions.project_id')
            ->selectRaw('YEAR(transaction_date) as year, sum(value_amount) as data')
            ->where('project_transactions.value_currency', $currency)
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->limit($no_of_years)
            ->get();

        return $report;
        
    }

    public function reportOnBudgetingtrends(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'currency' => 'nullable|string',
            'number_of_years' => 'nullable|numeric'

        ]); 
        $currency = $request->currency ?? 'USD';
        $no_of_years = $request->number_of_years ?? 12;
        if ($validator->fails()) {
                
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

       $report = DB::table('projects')
            ->join('project_budgets', 'projects.id', '=', 'project_budgets.project_id')
            ->selectRaw('YEAR(period_start) as year, sum(value_amount) as data')
            ->where('project_budgets.value_currency', $currency)
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->limit($no_of_years)
            ->get();

        return $report;
        
    }

    public function reportOnFundingBySector(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'currency' => 'nullable|string',
            'number_of_years' => 'nullable|numeric'

        ]); 
        $currency = $request->currency ?? 'USD';
        $no_of_years = $request->number_of_years ?? 6;
        if ($validator->fails()) {
                
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

       $report = DB::table('projects')
            ->join('project_transactions', 'projects.id', '=', 'project_transactions.project_id')
            ->join('project_sectors', 'projects.id', '=', 'project_sectors.project_id')
            ->selectRaw('project_sectors.code as sector, project_sectors.vocabulary as vocabulary, sum(value_amount) as data')
            ->where('project_transactions.value_currency', $currency)
            ->groupBy('sector', 'vocabulary')
          //  ->orderBy('data', 'desc')
            //->limit($no_of_years)
            ->get();
        $filtered = $report->map(function ($item) {
            return [
                'sector' => $this->getSectorCode($item->vocabulary, $item->sector),
                'data' => $item->data
            ];
        });
        return $filtered;
        
    }

    public function reportOnFundingBySource(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'currency' => 'nullable|string',
            'number_of_years' => 'nullable|numeric'

        ]); 
        $currency = $request->currency ?? 'USD';
        $no_of_years = $request->number_of_years ?? 6;
        if ($validator->fails()) {
                
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

       $report = DB::table('projects')
            ->join('project_transactions', 'projects.id', '=', 'project_transactions.project_id')
            ->join('project_transaction_provider_org', 'project_transactions.id', '=', 'project_transaction_provider_org.project_transaction_id')
            ->selectRaw('project_transaction_provider_org.organisation_id as organisation, sum(value_amount) as data')
            ->where('project_transactions.value_currency', $currency)
            ->groupBy('organisation')
           // ->orderBy('data', 'desc')
            //->limit($no_of_years)
            ->get();
        $filtered = $report->map(function ($item) {
            return [
                'organisation' => Organisation::find($item->organisation)->name ?? 'unknown',
                'data' => $item->data
            ];
        });
        return $filtered;
        
    }

    public function reportOnFundingByState(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'currency' => 'nullable|string',
            'number_of_years' => 'nullable|numeric'

        ]); 
        $currency = $request->currency ?? 'USD';
        $no_of_years = $request->number_of_years ?? 12;
        if ($validator->fails()) {
                
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

       $report = DB::table('projects')
            ->join('project_transactions', 'projects.id', '=', 'project_transactions.project_id')
            ->join('project_locations', 'projects.id', '=', 'project_locations.project_id')
            ->selectRaw('project_locations.state_id as state, sum(value_amount) as data')
            ->where('project_transactions.value_currency', $currency)
            ->groupBy('state')
           // ->orderBy('data', 'desc')
            //->limit($no_of_years)
            ->get();
        $filtered = $report->map(function ($item) {
            return [
                'state' => State::find($item->state)->name ?? 'none',
                'data' => $item->data
            ];
        });
        return $filtered;
        
    }

    public function reportOnTotalProjectstrends(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'currency' => 'nullable|string',
            'number_of_years' => 'nullable|numeric'

        ]); 
        $no_of_years = $request->number_of_years ?? 12;
        if ($validator->fails()) {
                
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

       $report = DB::table('projects')
            ->join('project_activity_dates', 'projects.id', '=', 'project_activity_dates.project_id')
            ->selectRaw('YEAR(iso_date) as year, count(DISTINCT projects.id) as data')
            ->where('project_activity_dates.type', 1)
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->limit($no_of_years)
            ->get();

        return $report;
        
    }

    public function reportOnTotalProjectsInprogresstrends(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'currency' => 'nullable|string',
            'number_of_years' => 'nullable|numeric'

        ]); 
        $no_of_years = $request->number_of_years ?? 12;
        if ($validator->fails()) {
                
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

       $report = DB::table('projects')
            ->join('project_activity_dates', 'projects.id', '=', 'project_activity_dates.project_id')
            ->selectRaw('YEAR(iso_date) as year, count(DISTINCT projects.id) as data')
            ->where('project_activity_dates.type', 2)
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->limit($no_of_years)
            ->get();

        return $report;
        
    }

    public function reportOnTotalProjectsCompletedtrends(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'currency' => 'nullable|string',
            'number_of_years' => 'nullable|numeric'

        ]); 
        $no_of_years = $request->number_of_years ?? 12;
        if ($validator->fails()) {
                
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

       $report = DB::table('projects')
            ->join('project_activity_dates', 'projects.id', '=', 'project_activity_dates.project_id')
            ->selectRaw('YEAR(iso_date) as year, count(DISTINCT projects.id) as data')
            ->where('project_activity_dates.type', 4)
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->limit($no_of_years)
            ->get();

        return $report;
        
    }

    public function reportOnTotalFunding(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'currency' => 'nullable|string',
            'year' => 'nullable|numeric'

        ]); 
       
        if ($validator->fails()) {
                
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

        if(!$request->has('year')) {

            $result = ProjectTransaction::where('transaction_type_code',1)->sum('value_amount');
        }
        

        if ($request->has('year')) {

            $year = $request->year;
            
            $result = ProjectTransaction::where('transaction_type_code',1)
                        ->whereYear('transaction_date', $year)
                        ->sum('value_amount');
        }
        

        return $result;
    }

    public function reportSummaryPerState(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'currency' => 'nullable|string',
            'number_of_years' => 'nullable|numeric'

        ]); 
        $currency = $request->currency ?? 'USD';
        $no_of_years = $request->number_of_years ?? 6;
        if ($validator->fails()) {
                
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

       $report = DB::table('projects')
            ->join('project_transactions', 'projects.id', '=', 'project_transactions.project_id')
            ->join('project_locations', 'projects.id', '=', 'project_locations.project_id')
            ->join('project_participating_orgs', 'projects.id', '=', 'project_participating_orgs.project_id')
            ->selectRaw('project_locations.state_id as state, count(project_participating_orgs.id) as count_orgs, count(DISTINCT projects.id) as count_projects, sum(value_amount) as data')
            ->where('project_transactions.value_currency', $currency)
            ->groupBy('state')
          //  ->orderBy('data', 'desc')
            //->limit($no_of_years)
            ->get();
        $filtered = $report->map(function ($item) {
            return [
                'state' => State::find($item->state)->name ?? 'none',
                'wikidataid' => State::find($item->state)->wikidataid ?? '0',
                'funding' => $this->convertToInternationalCurrencySystem($item->data),
                'projects' => $item->count_projects,
                'organisations' => $item->count_orgs
            ];
        });
        return $filtered;
        
    }

    private function getSectorCode($vocabulary, $code)
    {
        $sectorVocabulary = iati_get_code_value('SectorVocabulary', $vocabulary);
        if ($sectorVocabulary && $sectorVocabulary->code == '2') {
            return iati_get_code_value('SectorCategory', $code)->name ?? 'unknown';
        }

        if ($sectorVocabulary && $sectorVocabulary->code == '7') {
            return iati_get_code_value('UNSDG-Goals', $code)->name ?? 'unknown';
        }

        if ($sectorVocabulary && $sectorVocabulary->code == '8') {
            return iati_get_code_value('UNSDG-Targets', $code)->name ?? 'unknown';
        }
       
       return iati_get_code_value('Sector', $code)->name ?? 'unknown';
    }

    private  function convertToInternationalCurrencySystem ($value) 
    {

        if (abs($value) >= 1.0e+9) return round((abs($value) / 1.0e+9), 2) . "B";
        if (abs($value) >= 1.0e+6) return round((abs($value) / 1.0e+6), 2). "M";
        if (abs($value) >= 1.0e+3) return round((abs($value) / 1.0e+3), 2). "K";
        return abs($value);

    }

    
 

}