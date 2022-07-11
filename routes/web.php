<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaultController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\PopController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RemarkController;
use App\Http\Controllers\AccountManagerController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\RectificationController;
use App\Http\Controllers\Permit\PermitController;
use App\Http\Controllers\Permit\RequestPermitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;

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
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('faults', FaultController::class);
Route::resource('customers', CustomerController::class);
Route::resource('cities', CityController::class);
Route::resource('locations', LocationController::class);
Route::resource('links', LinkController::class);
Route::resource('pops', PopController::class);
Route::resource('account_managers', AccountManagerController::class);
Route::resource('assessments', AssessmentController::class);
Route::resource('rectify', RectificationController::class);
Route::resource('request-permits', PermitController::class);
Route::resource('permits', RequestPermitController::class);
Route::post('faults/{fault}/remarks', [RemarkController::class,'store']);
Route::get('suburb/{id}', [FaultController::class,'findSuburb'])->name('suburb');
Route::get('link/{id}', [FaultController::class,'findLink'])->name('link');
Route::get('pop/{id}', [FaultController::class,'findPop'])->name('pop');

//Users
Route::resource('user',UserController::class);
Route::get('/profile',[UserController::class,'profile'])->name('user.profile');
Route::post('/profile',[UserController::class,'postProfile'])->name('user.postProfile');

//Permisions
Route::resource('permission',PermissionController::class);

//Roles
Route::resource('role',RoleController::class);

//Departments
Route::resource('/departments', DepartmentController::class);

/// axios requests

Route::get('/getAllPermission',[PermissionController::class,'getAllPermissions']);
Route::post('/postRole',[RoleController::class,'store']);
Route::get("/getAllUsers", [UserController::class,"getAll"]);
Route::get("/getAllRoles", [RoleController::class,"getAll"]);
Route::get("/getAllPermissions", [PermissionController::class,'getAll']);

/////////////axios create user
Route::post('/account/create', [UserController::class,'store']);
Route::put('/account/update/{id}', [UserController::class,'update']);
Route::delete('/delete/user/{id}', [UserController::class,'delete']);
Route::get('/search/user', [UserController::class,'search']);


