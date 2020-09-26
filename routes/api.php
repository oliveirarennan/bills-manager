<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\BillStatusController;
use App\Http\Controllers\Api\UserController;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('auth/login', [AuthController::class, 'login']);

Route::group([
    'middleware' => ['apiJwt'],
    'prefix' => 'auth',
], function () {
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::group([
    'middleware' => ['apiJwt'],
    'prefix' => 'users',
], function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('show', [UserController::class, 'show']);
    Route::post('create', [UserController::class, 'store']);
    Route::put('update/{user}', [UserController::class, 'update']);
    Route::delete('delete/{user}', [UserController::class, 'destroy']);
});

Route::group([
    'middleware' => ['apiJwt'],
    'prefix' => 'bills',
], function () {
    Route::get('/', [BillController::class, 'index']);
    Route::get('show/{bill}', [BillController::class, 'show']);
    Route::post('create', [BillController::class, 'store']);
    Route::put('update/{bill}', [BillController::class, 'update']);
    Route::patch('status/update/{bill}', [BillStatusController::class, 'update']);
    Route::delete('delete/{bill}', [BillController::class, 'destroy']);
});
Route::fallback(function () {
    return response()->json(['message' => 'Not Found!'], 404);
});
