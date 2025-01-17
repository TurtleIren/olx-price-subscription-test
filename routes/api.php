<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\UserController;

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

//Route::middleware('auth:sanctum')->group(function () {
//    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
//    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
//    Route::delete('/subscriptions/{id}', [SubscriptionController::class, 'destroy']);
////    Route::apiResource('subscriptions', 'SubscriptionController');
//});

Route::group([
    'as' => 'api.',
    'namespace' => 'Api',
    //'middleware' => ['auth:api']
], function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/password/reset', [UserController::class, 'resetPassword']);

    Route::group([
        'middleware' => ['auth:sanctum']
        ], function () {
        Route::get('/subscriptions', [SubscriptionController::class, 'index']);
        Route::post('/subscriptions', [SubscriptionController::class, 'store']);
        Route::delete('/subscriptions/{id}', [SubscriptionController::class, 'destroy']);
    });

    Route::post('/testurl', [SubscriptionController::class, 'test']);
    Route::get('/test2', [SubscriptionController::class, 'test2']);
    Route::redirect('/', '/api/documentation');
});

//Route::redirect('/', '/api/documentation');
//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
