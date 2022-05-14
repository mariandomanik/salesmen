<?php

use App\Http\Controllers\CodelistsController;
use App\Http\Controllers\SalesmenController;
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

//token 1|KgFkUI2urZDAd8GdxgeE9FAy6itmS8RTNY1ioeuU
Route::middleware(['auth:sanctum'])->group(static function () {
    Route::post('/salesmen', [SalesmenController::class, 'store'])
        ->name('salesman.store');

    Route::get('/salesmen/{salesman?}', [SalesmenController::class, 'show'])
        ->name('salesmen.get');

    Route::put('/salesmen/{salesman}', [SalesmenController::class, 'update'])
        ->name('salesmen.update');

    Route::delete('/salesmen/{salesman}', [SalesmenController::class, 'delete'])
        ->name('salesmen.delete');

    Route::get('/codelist', [CodelistsController::class, 'show'])
        ->name('codelist.show');

});
