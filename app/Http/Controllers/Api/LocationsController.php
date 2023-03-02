<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\TranslationsResource;
use App\Models\County;
use App\Models\Language;
use App\Models\Payam;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class LocationsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum','isapproved'])->only('appTranslations');
    }

    public function getStates(Request $request) 
    {
        $states = State::query()->select(['id','name'])->get();
        return response(['locations' => $states]);
    }

           /**
     * Suggest roles.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function searchCounty(Request $request)
    {
        $this->validate($request, [
            'id' => 'nullable|integer',
            'related_id' => 'nullable|integer',
            'name' => 'nullable|min:3',
            'limit' => 'nullable|integer|min:1',
        ]);
        
        if ($request->filled('id')) {
           
            $location = County::query()
                ->select(['id', 'name'])
                ->where('id', $request->id)->get();
            if($request->filled('related_id'))
            {
                $location_related = County::query()
                 ->select(['id', 'name'])
                 ->where('state_id', $request->related_id)
                 ->whereNotIn('id',[$request->id])->get();
                 $locations = $location->merge($location_related);
                return response(['locations' =>$locations]); 
            }
            return response($location);
            
        }
        else if ($request->filled('related_id')) {
            
             $location = County::query()
                 ->select(['id', 'name'])
                 ->where('state_id', $request->related_id)->get();
 
             
             return response(['locations' => $location]);
         }
       
        $query = County::query()
            ->select(['id', 'name'])
            ->where('name', 'like', "%{$request->name}%");

        

        $locations = $query->take($request->input('limit', 10))->get();
      
        return response(['locations' => $locations]);
    }

    public function searchPayam(Request $request)
    {
        $this->validate($request, [
            'id' => 'nullable|integer',
            'related_id' => 'nullable|integer',
            'name' => 'nullable|min:3',
            'limit' => 'nullable|integer|min:1',
        ]);
        
        if ($request->filled('id')) {
           
            $location = Payam::query()
                ->select(['id', 'name'])
                ->where('id', $request->id)->get();
            if($request->filled('related_id'))
            {
                $location_related = Payam::query()
                 ->select(['id', 'name'])
                 ->where('county_id', $request->related_id)
                 ->whereNotIn('id',[$request->id])->get();
                 $locations = $location->merge($location_related);
                return response(['locations' =>$locations]); 
            }
            return response($location);
            
        }
        else if ($request->filled('related_id')) {
            
             $location = Payam::query()
                 ->select(['id', 'name'])
                 ->where('county_id', $request->related_id)->get();
 
             
             return response(['locations' => $location]);
         }
       
        $query = Payam::query()
            ->select(['id', 'name'])
            ->where('name', 'like', "%{$request->name}%");

        

        $locations = $query->take($request->input('limit', 10))->get();
     
        return response(['locations' => $locations]);
    }
}
