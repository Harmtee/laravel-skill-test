<?php

use App\Http\Controllers\StockController;
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

Route::get('/', [StockController::class, 'index']);
Route::post('/', [StockController::class, 'store']);
Route::put('/{id}', [StockController::class, 'update']);
Route::delete('/{id}', [StockController::class, 'destroy']);
