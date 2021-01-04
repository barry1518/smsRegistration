<?php

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::post('login', [AccessTokenController::class, 'issueToken'])
//    ->middleware(['api-login', 'throttle']);
Route::post('sendemail', 'Api\SendEmailController@mail');

//landing page
Route::post('/landing/register/user', 'Api\AuthLandingController@registerUser');
Route::post('/landing/register/pro', 'Api\AuthLandingController@registerPro');

Route::post('/register/pro', 'Api\AuthController@registerPro');
Route::post('/login', 'Api\AuthController@login');

Route::post('password/email', 'Api\ForgotPasswordController@sendResetLinkEmail');
Route::post('password/reset', 'Api\ResetPasswordController@reset');

Route::get('/email/resend','Api\VerificationController@resend')->name('verification.resend');
Route::get('/email/verify/{id}/{hash}', 'Api\VerificationController@verify')->name('verification.verify');

Route::apiResource('/tasks', 'Api\TasksController')->middleware('auth:api');

//Route::get('/cleaners', 'Api\CleanersController@index');
//Route::post('/cleaners', 'Api\CleanersController@show');
//Route::post('/cleaners', 'Api\CleanersController@update');
//Route::post('/cleaners', 'Api\CleanersController@remove');
