<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', [\App\Http\Controllers\Api\LoginController::class, 'index']);
Route::post('login', [\App\Http\Controllers\Api\LoginController::class, 'index']);

Route::middleware(['auth.ingco.storehub'])->group(function () {

    Route::get('logout', [\App\Http\Controllers\Api\LoginController::class, 'logout']);
    Route::post('logout', [\App\Http\Controllers\Api\LoginController::class, 'logout']);

    Route::get('', [\App\Http\Controllers\UtilityController::class, 'utility']);
    Route::post('sync', [\App\Http\Controllers\UtilityController::class, 'sync']);

    Route::get('sales-terminal', function () {return view('sales.terminal.index');});
});
