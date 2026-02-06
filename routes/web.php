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
use App\Http\Controllers\ChiefTechEscalationsController;
use App\Http\Controllers\NocClearFaultsController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\RFOController;
use App\Http\Controllers\TechnicianConfigController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\ResolvedController;
use App\Http\Controllers\ZoneController;

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
    return Auth::check()
        ? redirect()->route('home')
        : redirect()->route('login');
});

Auth::routes();

Route::group(['middleware' => ['auth']], function() {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    // Reports dashboard
    Route::get('/dashboard/reports', [DashboardController::class, 'reports'])->name('dashboard.reports');
    // Performance Dashboard
    Route::get('/performance', [PerformanceController::class, 'index'])->name('performance.index');
    Route::resource('users', UserController::class);
    // Toggle user access (enable/disable)
    Route::patch('users/{user}/access', [UserController::class, 'updateAccess'])->name('users.access');
    // Admin change password for a specific user
    Route::put('users/{user}/change-password', [UserController::class, 'changePassword'])->name('users.change-password');
    Route::resource('roles', RoleController::class);
    Route::resource('permission',PermissionController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('sections', SectionController::class);
    Route::resource('positions', PositionController::class);
    // Account Managers: faults for customers they manage (place BEFORE resource to avoid capture by faults/{fault})
    Route::get('faults/managed', [FaultController::class, 'managedCustomers'])->name('manage.faults');
    Route::resource('faults', FaultController::class);
    // Exports: bypass pagination, honor current filters
    Route::get('faults/export/pdf', [FaultController::class, 'exportPdf'])->name('faults.export.pdf');
    Route::get('faults/export/csv', [FaultController::class, 'exportCsv'])->name('faults.export.csv');
    // Customers: client-side validation helper (must be BEFORE resource route)
    Route::get('customers/check-account-number', [CustomerController::class, 'checkAccountNumber'])->name('customers.check-account-number');
    Route::get('customers/check-customer-name', [CustomerController::class, 'checkCustomerName'])->name('customers.check-customer-name');
    // Links: client-side validation helper (must be BEFORE resource route)
    Route::get('links/check-link-name', [LinkController::class, 'checkLinkName'])->name('links.check-link-name');
    Route::get('links/check-jcc-number', [LinkController::class, 'checkJccNumber'])->name('links.check-jcc-number');
    Route::resource('customers', CustomerController::class);
    Route::put('customers/{id}/disconnect', [CustomerController::class,'disconnect'])->name('customers.disconnect');
    Route::put('customers/{id}/reconnect', [CustomerController::class,'reconnect'])->name('customers.reconnect');
    Route::put('customers/{id}/reconnect-decommissioned', [CustomerController::class,'reconnectDecommissioned'])->name('customers.reconnect_decommissioned');
    Route::put('customers/{id}/decommission', [CustomerController::class,'decommission'])->name('customers.decommission');
    Route::resource('cities', CityController::class);
    Route::resource('zones', ZoneController::class);
    Route::resource('locations', LocationController::class);
    Route::resource('links', LinkController::class);
    // AJAX: fetch links for a given customer
    Route::get('links/customer/{customer}', [LinkController::class, 'linksForCustomer'])->name('links.by-customer');
    // AJAX: autosave updates for a link
    Route::post('links/{link}/autosave', [LinkController::class, 'autosave'])->name('links.autosave');
    Route::resource('pops', PopController::class);
    Route::resource('account_managers', AccountManagerController::class);
    Route::resource('assessments', AssessmentController::class);
    Route::resource('rectify', RectificationController::class);
    Route::post('assign/perform', [AssignController::class, 'assignFault'])->name('assign.perform');
    Route::resource('assign', AssignController::class);
    Route::resource('chief-tech-clear', ChiefTechClearFaultsController::class);
    Route::resource('noc-clear', NocClearFaultsController::class);
    // NOC revoke technician-cleared fault back to rectification
    Route::put('noc-clear/{id}/revoke', [NocClearFaultsController::class, 'revoke'])->name('noc-clear.revoke');
    // Cleared by NOC within 24 hours
    Route::get('resolved', [ResolvedController::class, 'index'])->name('resolved.index');
    Route::post('resolved/{fault}/revoke', [ResolvedController::class, 'revoke'])->name('resolved.revoke');
    Route::resource('permits', PermitController::class);
    Route::resource('finance', FinanceController::class);
    Route::resource('my_faults', MyFaultController::class);
    Route::post('my_faults/{id}/refer', [MyFaultController::class, 'refer'])->name('my_faults.refer');
    Route::post('my_faults/{fault}/escalate', [MyFaultController::class, 'escalate'])->name('my_faults.escalate');
    Route::resource('department_faults', DepartmentFaultController::class);
    Route::get('referred-faults', [DepartmentFaultController::class, 'referred'])->name('referred_faults.index');
    Route::post('referrals/{referral}/complete', [DepartmentFaultController::class, 'completeReferral'])->name('referrals.complete');
    Route::resource('rfos', RFOController::class);
    Route::resource('request-permit', RequestPermitController::class);
    Route::resource('stores', StoreController::class);
    Route::put('disconnect/{id}/disconnect', [FinanceController::class,'disconnect'])->name('disconnect');
    Route::put('reconnect/{id}/reconnect', [FinanceController::class,'reconnect'])->name('reconnect');
    Route::put('decommission/{id}/decommission', [FinanceController::class,'decommission'])->name('decommission');
    Route::post('faults/{fault}/remarks', [RemarkController::class,'store']);
    Route::get('suburb/{id}', [FaultController::class,'findSuburb'])->name('suburb');
    Route::get('link/{id}', [FaultController::class,'findLink'])->name('link');
    Route::get('pop/{id}', [FaultController::class,'findPop'])->name('pop');
    Route::get('section/{id}', [DepartmentController::class,'findSection'])->name('section');
    Route::get('position/{id}', [DepartmentController::class,'findPosition'])->name('position');
    Route::put('auto/{id}/auto', [AssessmentController::class,'assign'])->name('auto');
    Route::get('stores/{id}', [StoreController::class,'findstores'])->name('stores');

    Route::get('call-centre/reports', [\App\Http\Controllers\CallCentreController::class, 'index'])->name('call_centre.reports');

    // Add change password routes
    Route::get('/password/change', [UserController::class,'getPassword'])->name('user.password.change');
    Route::post('/password/change', [UserController::class,'postPassword'])->name('user.password.update');

    // Technician settings (single route with modal for auto-assign)
    Route::get('technicians/config', [TechnicianConfigController::class, 'config'])->name('technicians.config');
    Route::post('technicians/settings', [TechnicianConfigController::class, 'updateSettings'])->name('technicians.settings.update');
    Route::post('technicians/settings/regions', [TechnicianConfigController::class, 'updateRegions'])->name('technicians.regions.update');
    // Auto-save endpoints
    Route::post('technicians/settings/ajax', [TechnicianConfigController::class, 'updateSettingsAjax'])->name('technicians.settings.ajax');
    Route::post('technicians/users/{user}/setting', [TechnicianConfigController::class, 'updateUserSetting'])->name('technicians.user.setting');

    Route::get('chief-tech/escalations', [ChiefTechEscalationsController::class, 'index'])->name('chief-tech-escalations.index');
    Route::post('chief-tech/escalations/{fault}/refer', [ChiefTechEscalationsController::class, 'refer'])->name('chief-tech-escalations.refer');
    Route::post('chief-tech/escalations/{fault}/return', [ChiefTechEscalationsController::class, 'returnToRectification'])->name('chief-tech-escalations.return');
    Route::post('chief-tech/escalations/{fault}/escalate-manager', [ChiefTechEscalationsController::class, 'escalateToManager'])->name('chief-tech-escalations.escalate-manager');
    Route::post('chief-tech/escalations/{fault}/return-from-manager', [ChiefTechEscalationsController::class, 'downgradeFromManager'])->name('chief-tech-escalations.return-from-manager');
});


Route::get('department-faults', [DepartmentFaultController::class,'getSections'])->name('department-faults');
//Users
Route::get('/profile',[UserController::class,'profile'])->name('user.profile');
Route::post('/profile',[UserController::class,'postProfile'])->name('user.postProfile');


Route::get('getfaults', [FaultController::class,'faults'])->name('getfaults');
Route::get('getusers', [UserController::class,'getUsers'])->name('getusers');



Route::put('auto', [AssessmentController::class,'assign']);

// Test routes for Infobip integration (remove in production)
Route::view('/test-infobip', 'test-infobip')->name('test-infobip');
