<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Customer\RegisterController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::namespace('API')->group(function(){

    #User APP
    Route::prefix('customer')->namespace('Customer')->group(function()
    {
       Route::post('register',[RegisterController::class,'register']);
       Route::post('login',[RegisterController::class,'login']);
       Route::post('forgot-password',[RegisterController::class,'forgotPassword']);
       Route::post('check-token',[RegisterController::class,'checkToken']);
       Route::post('check-user-account',[RegisterController::class,'checkUserAccount']);

       Route::group(['middleware' => 'userMahjconApi'], function()
       {
            Route::post('profile',[RegisterController::class,'getProfile']);
            Route::post('update-profile',[RegisterController::class,'updateProfile']);
            Route::post('change-password',[RegisterController::class,'changePassword']);
            Route::post('subscription-create',[RegisterController::class,'subscriptionCreate']);
            Route::post('payment-history',[RegisterController::class,'subscriptionHistory']);
            Route::post('rating-save',[RegisterController::class,'ratingSave']);
            Route::post('delete-user-account',[RegisterController::class,'deleteUserAccount']);

       });



    });

});
