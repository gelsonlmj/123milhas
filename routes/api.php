<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FlightsController;

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

Route::prefix('flights')->group(function () {
    Route::get('/', [FlightsController::class, 'flights']);
    Route::get('/groups-available', [FlightsController::class, 'groupsAvailable']);
    Route::get('/lowest-price', [FlightsController::class, 'lowestPrice']);
    Route::get('/informations', [FlightsController::class, 'informationsFlight']);
});
