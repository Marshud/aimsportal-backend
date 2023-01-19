<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\TranslationsResource;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class LanguagesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum','isapproved'])->only('appTranslations');
    }

    public function index(Request $request) 
    {
        return response()->success(LanguageResource::collection(Language::all()));
    }

    public function appTranslations(Request $request)
    {
        
        App::setLocale($request->user()->language ?? $request->language ?? 'en');
        return response()->success(Lang::get('website'));
        
    }
}
