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
use App\Http\Controllers\AssignController;
use App\Http\Controllers\Permit\PermitController;
use App\Http\Controllers\Permit\RequestPermitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MyFaultController;
use App\Http\Controllers\DepartmentFaultController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ChiefTechClearFaultsController;
use App\Http\Controllers\NocClearFaultsController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\StoreController;
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

Route::group(['middleware' => ['auth']], function() {
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permission',PermissionController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('sections', SectionController::class);
    Route::resource('positions', PositionController::class);
    Route::resource('faults', FaultController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('cities', CityController::class);
    Route::resource('locations', LocationController::class);
    Route::resource('links', LinkController::class);
    Route::resource('pops', PopController::class);
    Route::resource('account_managers', AccountManagerController::class);
    Route::resource('assessments', AssessmentController::class);
    Route::resource('rectify', RectificationController::class);
    Route::resource('assign', AssignController::class);
    Route::resource('chief-tech-clear', ChiefTechClearFaultsController::class);
    Route::resource('noc-clear', NocClearFaultsController::class);
    Route::resource('permits', PermitController::class);
    Route::resource('finance', FinanceController::class);
    Route::resource('my_faults', MyFaultController::class);
    Route::resource('department_faults', DepartmentFaultController::class);
    Route::resource('request-permit', RequestPermitController::class);
    Route::resource('stores', StoreController::class);

    Route::put('disconnect/{id}/disconnect', [FinanceController::class,'disconnect'])->name('disconnect');
    Route::put('reconnect/{id}/reconnect', [FinanceController::class,'reconnect'])->name('reconnect');
    Route::put('deny/{id}/denied', [StoreController::class,'denied'])->name('deny');
    Route::put('issue/{id}/issued', [StoreController::class,'issued'])->name('issue');

    Route::post('faults/{fault}/remarks', [RemarkController::class,'store']);
    Route::get('suburb/{id}', [FaultController::class,'findSuburb'])->name('suburb');
    Route::get('link/{id}', [FaultController::class,'findLink'])->name('link');
    Route::get('pop/{id}', [FaultController::class,'findPop'])->name('pop');
    Route::get('section/{id}', [DepartmentController::class,'findSection'])->name('section');
    Route::get('position/{id}', [DepartmentController::class,'findPosition'])->name('position');
    Route::put('auto/{id}/auto', [AssessmentController::class,'assign'])->name('auto');
    Route::get('stores/{id}', [StoreController::class,'findstores'])->name('stores');
});


Route::get('department-faults', [DepartmentFaultController::class,'getSections'])->name('department-faults');
//Users
Route::get('/profile',[UserController::class,'profile'])->name('user.profile');
Route::post('/profile',[UserController::class,'postProfile'])->name('user.postProfile');


Route::get('getfaults', [FaultController::class,'faults'])->name('getfaults');
Route::get('getusers', [UserController::class,'getUsers'])->name('getusers');



Route::put('auto', [AssessmentController::class,'assign']);




