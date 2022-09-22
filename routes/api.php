<?php

use App\Http\Controllers\Api\OrganisationCategoryController;
use App\Http\Controllers\Api\OrganisationController;
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
    Route::get('/{id}', [UserController::class,'show']);
    Route::post('/validate-email', [UserController::class,'startEmailSignup']);
    Route::post('/register', [UserController::class,'register']);
    Route::post('/login', [UserController::class,'authenticate']);
    Route::delete('/logout', [UserController::class,'deauthenticate']);
    Route::post('/update-status/{id}', [UserController::class,'updateStatus']);
    Route::post('/update-user/{id}', [UserController::class,'updateUser']);
    Route::delete('/delete/{id}', [UserController::class,'destroy']);
});
