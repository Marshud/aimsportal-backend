<?php

use App\Http\Controllers\Api\CurrencyConvertController;
use App\Http\Controllers\Api\CustomFieldsController;
use App\Http\Controllers\Api\IatiHelperController;
use App\Http\Controllers\Api\LanguagesController;
use App\Http\Controllers\Api\LocationsController;
use App\Http\Controllers\Api\OrganisationCategoryController;
use App\Http\Controllers\Api\OrganisationController;
use App\Http\Controllers\Api\ProjectsController;
use App\Http\Controllers\Api\ReportsController;
use App\Http\Controllers\Api\SystemSettingsController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::apiResources([
    'organisations' => OrganisationController::class,
    'organisation-categories' => OrganisationCategoryController::class,
]);

Route::get('organisations/{id}/users', [OrganisationController::class, 'listOrganisationUsers']);

Route::group(['prefix' => 'users', 'as' => 'users.'],function() {
    Route::get('/', [UserController::class,'listOrganisationUsers']);
    Route::get('/profile', [UserController::class,'profile']);
    Route::get('/supers', [UserController::class, 'listSuperAdministrators']);
    Route::get('/{id}', [UserController::class,'show']);
    Route::post('/validate-email', [UserController::class,'startEmailSignup']);
    Route::post('/register', [UserController::class,'register']);
    Route::post('/login', [UserController::class,'authenticate']);
    Route::post('/guest-login', [UserController::class,'authenticateGuest']);
    Route::delete('/logout', [UserController::class,'deauthenticate']);
    Route::post('/update-status/{id}', [UserController::class,'updateStatus']);
    Route::post('/update-user/{id}', [UserController::class,'updateUser']);
    Route::delete('/delete/{id}', [UserController::class,'destroy']);
    Route::post('/reset-password', [UserController::class, 'sendPasswordResetLink']);
    Route::get('/reset-password/{user}', [UserController::class, 'passwordReset'])->name('reset.password');
    Route::post('/update-password/{user}', [UserController::class, 'updatePassword']);    
    Route::post('/super', [UserController::class, 'createSuperAdministrator']);
});

Route::group(['prefix' => 'custom-fields', 'as' => 'custom.fields.'], function() {
    Route::get('/', [CustomFieldsController::class, 'index']);
    Route::post('/', [CustomFieldsController::class, 'store']);
    Route::get('/{id}', [CustomFieldsController::class, 'show']);
    Route::post('/{id}', [CustomFieldsController::class, 'update']);
    Route::delete('/{id}', [CustomFieldsController::class, 'destroy']);
});

Route::group(['prefix' => 'projects', 'as' => 'projects.'], function() {
    Route::get('/', [ProjectsController::class, 'index']);
    Route::post('/', [ProjectsController::class, 'store']);
    Route::post('/search', [ProjectsController::class, 'index']);
    Route::put('/{id}', [ProjectsController::class, 'update']);
    Route::get('/{id}', [ProjectsController::class, 'show']);
    Route::delete('/{id}', [ProjectsController::class, 'destroy']);
    Route::delete('/participating-org/{id}', [ProjectsController::class, 'deleteParticipatingOrg']);
    Route::delete('/budget/{id}', [ProjectsController::class, 'deleteProjectBudget']);
    Route::delete('/sector/{id}', [ProjectsController::class, 'deleteProjectSector']);
    Route::delete('/recipient-region/{id}', [ProjectsController::class, 'deleteRecipientRegion']);
    Route::delete('/transaction/{id}', [ProjectsController::class, 'deleteTransaction']);
    Route::delete('/location/{id}', [ProjectsController::class, 'deleteLocation']);
});

Route::group(['prefix' => 'general', 'as' => 'general.'], function() {
    Route::group(['prefix' => 'codelists', 'as' => 'codelists.'], function() {
        Route::any('get-options', [IatiHelperController::class, 'getCodelistOptions']);
        Route::any('get-value', [IatiHelperController::class, 'getCodelistValue']);
    });
    Route::group(['prefix' => 'languages', 'as' => 'languages.'], function() {
        Route::any('/', [LanguagesController::class, 'index']);
        Route::any('get-translations', [LanguagesController::class, 'appTranslations']);
    });
    Route::group(['prefix' => 'currency', 'as' => 'currency.'], function() {
        Route::any('convert', [CurrencyConvertController::class, 'index']);
    });
    
});

Route::group(['prefix' => 'reports', 'as' => 'reports.'], function() {
    Route::any('funding-trend', [ReportsController::class, 'reportOnFundingTrends']);
    Route::any('funding-by-sector', [ReportsController::class, 'reportOnFundingBySector']);
    Route::any('funding-by-state', [ReportsController::class, 'reportOnFundingByState']);
    Route::any('funding-by-source', [ReportsController::class, 'reportOnFundingBySource']);
    Route::any('total-projects-trend', [ReportsController::class, 'reportOnTotalProjectstrends']);
    Route::any('total-projects-inprogress-trend', [ReportsController::class, 'reportOnTotalProjectsInprogresstrends']);
    Route::any('total-projects-completed-trend', [ReportsController::class, 'reportOnTotalProjectsCompletedtrends']);
    Route::any('funding-total', [ReportsController::class, 'reportOnTotalFunding']);
    Route::any('budgets-trend', [ReportsController::class, 'reportOnBudgetingTrends']);
    Route::any('summary-per-state', [ReportsController::class, 'reportSummaryPerState']);
    Route::any('organisations-count', [ReportsController::class, 'reportOrganisationCount']);
    Route::any('summary-per-county', [ReportsController::class, 'reportSummaryPerCounty']);
    Route::any('projects-count', [ReportsController::class, 'reportProjectCount']);

});

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function() {
    Route::get('system-settings', [SystemSettingsController::class, 'getSystemSettings']);
    Route::post('system-settings', [SystemSettingsController::class, 'storeSystemSettings']);
});

Route::group(['prefix' => 'locations', 'as' => 'locations.'], function() {
    Route::any('get-states', [LocationsController::class, 'getStates']);
    Route::any('get-counties', [LocationsController::class, 'searchCounty']);
    Route::any('get-payams', [LocationsController::class, 'searchPayam']);
});
