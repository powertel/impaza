<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
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
use App\Http\Controllers\RFOController;

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

Route::group(['middleware' => ['auth']], function() {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permission',PermissionController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('sections', SectionController::class);
    Route::resource('positions', PositionController::class);
    Route::resource('faults', FaultController::class);
    // Customers: client-side validation helper (must be BEFORE resource route)
    Route::get('customers/check-account-number', [CustomerController::class, 'checkAccountNumber'])->name('customers.check-account-number');
    Route::get('customers/check-customer-name', [CustomerController::class, 'checkCustomerName'])->name('customers.check-customer-name');
    // Links: client-side validation helper (must be BEFORE resource route)
    Route::get('links/check-link-name', [LinkController::class, 'checkLinkName'])->name('links.check-link-name');
    Route::get('links/check-jcc-number', [LinkController::class, 'checkJccNumber'])->name('links.check-jcc-number');
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
    // NOC revoke technician-cleared fault back to rectification
    Route::put('noc-clear/{id}/revoke', [NocClearFaultsController::class, 'revoke'])->name('noc-clear.revoke');
    Route::resource('permits', PermitController::class);
    Route::resource('finance', FinanceController::class);
    Route::resource('my_faults', MyFaultController::class);
    Route::resource('department_faults', DepartmentFaultController::class);
    Route::resource('rfos', RFOController::class);
    Route::resource('request-permit', RequestPermitController::class);
    Route::resource('stores', StoreController::class);
    Route::put('disconnect/{id}/disconnect', [FinanceController::class,'disconnect'])->name('disconnect');
    Route::put('reconnect/{id}/reconnect', [FinanceController::class,'reconnect'])->name('reconnect');
    Route::post('faults/{fault}/remarks', [RemarkController::class,'store']);
    Route::get('suburb/{id}', [FaultController::class,'findSuburb'])->name('suburb');
    Route::get('link/{id}', [FaultController::class,'findLink'])->name('link');
    Route::get('pop/{id}', [FaultController::class,'findPop'])->name('pop');
    Route::get('section/{id}', [DepartmentController::class,'findSection'])->name('section');
    Route::get('position/{id}', [DepartmentController::class,'findPosition'])->name('position');
    Route::put('auto/{id}/auto', [AssessmentController::class,'assign'])->name('auto');
    Route::get('stores/{id}', [StoreController::class,'findstores'])->name('stores');

    // Add change password routes
    Route::get('/password/change', [UserController::class,'getPassword'])->name('user.password.change');
    Route::post('/password/change', [UserController::class,'postPassword'])->name('user.password.update');

    // Technician settings (single route with modal for auto-assign)
    Route::get('technicians/config', [\App\Http\Controllers\TechnicianConfigController::class, 'config'])->name('technicians.config');
    Route::post('technicians/settings', [\App\Http\Controllers\TechnicianConfigController::class, 'updateSettings'])->name('technicians.settings.update');
    Route::post('technicians/settings/regions', [\App\Http\Controllers\TechnicianConfigController::class, 'updateRegions'])->name('technicians.regions.update');
    // Auto-save endpoints
    Route::post('technicians/settings/ajax', [\App\Http\Controllers\TechnicianConfigController::class, 'updateSettingsAjax'])->name('technicians.settings.ajax');
    Route::post('technicians/users/{user}/setting', [\App\Http\Controllers\TechnicianConfigController::class, 'updateUserSetting'])->name('technicians.user.setting');
});


Route::get('department-faults', [DepartmentFaultController::class,'getSections'])->name('department-faults');
//Users
Route::get('/profile',[UserController::class,'profile'])->name('user.profile');
Route::post('/profile',[UserController::class,'postProfile'])->name('user.postProfile');


Route::get('getfaults', [FaultController::class,'faults'])->name('getfaults');
Route::get('getusers', [UserController::class,'getUsers'])->name('getusers');



Route::put('auto', [AssessmentController::class,'assign']);




