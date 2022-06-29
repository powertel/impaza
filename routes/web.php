<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaultController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\PopController;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::resource('faults', FaultController::class);
Route::resource('customers', CustomerController::class);
Route::resource('cities', CityController::class);
Route::resource('locations', LocationController::class);
Route::resource('links', LinkController::class);
Route::resource('pops', PopController::class);
Route::get('suburb/{id}', [FaultController::class,'findSuburb'])->name('suburb');
Route::get('pop/{id}', [FaultController::class,'findPop'])->name('pop');
Route::get('link/{id}', [FaultController::class,'findLink'])->name('link');