<?php

use App\Http\Controllers\CodelistsController;
use App\Http\Controllers\SalesmenController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(static function () {
    Route::post('/salesmen', [SalesmenController::class, 'store'])
        ->middleware(['auth:sanctum', 'ability:salesmen-store'])
        ->name('salesman.store');

    Route::get('/salesmen/{salesman?}', [SalesmenController::class, 'show'])
        ->middleware(['auth:sanctum', 'ability:salesmen-get'])
        ->name('salesmen.get');

    Route::put('/salesmen/{salesman}', [SalesmenController::class, 'update'])
        ->middleware(['auth:sanctum', 'ability:salesmen-update'])
        ->name('salesmen.update');

    Route::delete('/salesmen/{salesman}', [SalesmenController::class, 'delete'])
        ->middleware(['auth:sanctum', 'ability:salesmen-delete'])
        ->name('salesmen.delete');

    Route::get('/codelist', [CodelistsController::class, 'show'])
        ->middleware(['auth:sanctum', 'ability:codelist-get'])
        ->name('codelist.show');
});

Route::get('/generateUserAllAbilities', static function () {
    $user = UserController::generateUser();
    $token = $user->createToken('salesmen_codelist_token')->plainTextToken;

    return response()->json([
        'token' => $token
    ]);

});


Route::get('/generateUserSalesmenOnly', static function () {
    $user = UserController::generateUser();
    $token = $user->createToken('salesmen_only_token', ['salesmen-store', 'salesmen-get', 'salesmen-delete', 'salesmen-update'])->plainTextToken;

    return response()->json([
        'token' => $token
    ]);
});

Route::get('/generateUserCodelistOnly', static function () {
    $user = UserController::generateUser();
    $token = $user->createToken('codelist_only_token', ['codelist-get'])->plainTextToken;

    return response()->json([
        'token' => $token
    ]);
});

Route::get('/generateUserSalesmenGetOnly', static function () {
    $user = UserController::generateUser();
    $token = $user->createToken('salesmen_get_only_token', ['salesmen-get'])->plainTextToken;

    return response()->json([
        'token' => $token
    ]);
});
