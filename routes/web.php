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
use App\Http\Controllers\TechnicianConfigController;

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
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permission',PermissionController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('sections', SectionController::class);
    Route::resource('positions', PositionController::class);
    // Account Managers: faults for customers they manage (place BEFORE resource to avoid capture by faults/{fault})
    Route::get('faults/managed', [FaultController::class, 'managedCustomers'])->name('manage.faults');
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
    Route::get('technicians/config', [TechnicianConfigController::class, 'config'])->name('technicians.config');
    Route::post('technicians/settings', [TechnicianConfigController::class, 'updateSettings'])->name('technicians.settings.update');
    Route::post('technicians/settings/regions', [TechnicianConfigController::class, 'updateRegions'])->name('technicians.regions.update');
    // Auto-save endpoints
    Route::post('technicians/settings/ajax', [TechnicianConfigController::class, 'updateSettingsAjax'])->name('technicians.settings.ajax');
    Route::post('technicians/users/{user}/setting', [TechnicianConfigController::class, 'updateUserSetting'])->name('technicians.user.setting');
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

Route::post('/test-infobip-send', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'phone' => ['required', 'string', 'min:3'],
        'message' => ['required', 'string', 'min:1'],
    ]);

    \Log::info('Test Infobip send attempt', [
        'phone' => $validated['phone'],
    ]);

    // Probe env and config presence to debug missing configuration
    $envApiKey = env('INFOBIP_API_KEY');
    $envWhatsapp = env('INFOBIP_WHATSAPP_NUMBER');
    \Log::info('Infobip config probe', [
        'env_api_key_set' => !empty($envApiKey),
        'env_whatsapp_set' => !empty($envWhatsapp),
        'cfg_api_key_set' => !empty(config('services.infobip.api_key')),
        'cfg_whatsapp_set' => !empty(config('services.infobip.whatsapp_number')),
        'base_url' => config('services.infobip.base_url'),
    ]);

    try {
        $service = app(\App\Services\InfobipService::class);
        $result = $service->sendWhatsAppTextDetailed($validated['phone'], $validated['message']);

        \Log::info('Test Infobip send result', [
            'success' => $result['success'] ?? null,
            'status' => $result['status'] ?? null,
            'body' => is_string($result['body'] ?? null) ? substr($result['body'], 0, 500) : $result['body'],
        ]);

        return response()->json([
            'success' => $result['success'],
            'status' => $result['status'],
            'body' => $result['body'],
            // Align keys with test page expectations
            'message' => $result['success'] ? 'Message sent to Infobip' : 'Failed to send message',
            'result' => $result['body'],
            'messageId' => (is_array($result['body'] ?? null) && isset($result['body']['messages'][0]['messageId'])) ? $result['body']['messages'][0]['messageId'] : null,
        ], $result['success'] ? 200 : ($result['status'] ?: 400));
    } catch (\Throwable $e) {
        \Log::error('Test Infobip send exception', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json([
            'success' => false,
            'error' => 'Exception during send',
            'message' => $e->getMessage(),
            'result' => null,
            'messageId' => null,
        ], 500);
    }
})->name('test.infobip.send');

// Status check route for Infobip WhatsApp messages
Route::post('/test-infobip-send-template', function (\Illuminate\Http\Request $request, \App\Services\InfobipService $svc) {
    $to = $request->input('to');
    $templateName = $request->input('templateName');
    $language = $request->input('language', 'en');
    $placeholders = $request->input('placeholders', []);

    if (is_string($placeholders)) {
        $placeholders = array_values(array_filter(array_map('trim', explode(',', $placeholders)), fn($x) => $x !== ''));
    }

    if (!$to || !$templateName) {
        return response()->json([
            'message' => 'Missing required fields: to, templateName',
            'result' => null,
            'messageId' => null,
            'status' => 422,
        ], 422);
    }

    $res = $svc->sendWhatsAppTemplateDetailed($to, $templateName, $language, is_array($placeholders) ? $placeholders : []);

    $messageId = null;
    if (is_array($res['body'])) {
        if (isset($res['body']['messages'][0]['messageId'])) {
            $messageId = $res['body']['messages'][0]['messageId'];
        } elseif (isset($res['body']['messageId'])) {
            $messageId = $res['body']['messageId'];
        }
    }

    return response()->json([
        'message' => $res['success'] ? 'Template sent successfully!' : 'Template send failed',
        'result' => $res['body'],
        'messageId' => $messageId,
        'status' => $res['status'],
    ], $res['success'] ? 200 : 400);
});
// Status check route for Infobip WhatsApp messages
Route::get('/test-infobip-status', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'messageId' => ['required', 'string', 'min:10'],
    ]);

    $messageId = $request->query('messageId');
    try {
        $service = app(\App\Services\InfobipService::class);
        $result = $service->getWhatsAppMessageStatus($messageId);

        \Log::info('Test Infobip status result', [
            'success' => $result['success'] ?? null,
            'status' => $result['status'] ?? null,
            'body' => is_string($result['body'] ?? null) ? substr($result['body'], 0, 500) : $result['body'],
        ]);

        return response()->json([
            'success' => $result['success'],
            'status' => $result['status'],
            'body' => $result['body'],
        ], $result['success'] ? 200 : ($result['status'] ?: 400));
    } catch (\Throwable $e) {
        \Log::error('Test Infobip status exception', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json([
            'success' => false,
            'error' => 'Exception during status query',
            'message' => $e->getMessage(),
        ], 500);
    }
})->name('test.infobip.status');




