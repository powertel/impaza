<?php

use App\Models\Fault;
use App\Services\FaultLifecycle;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test Case: Fault 1 (Section 2, Region East)
$fault = Fault::find(1);

if (!$fault) {
    echo "Fault 1 not found\n";
    exit;
}

echo "Testing Status 2 (Assessed) notification for Fault: {$fault->fault_ref_number}\n";
echo "Current Status: {$fault->status_id}\n";

// We simulate moving to status 2
// In a real scenario, this would be called from FaultController
try {
    FaultLifecycle::notifyStatusChange($fault, 2, 1, "Testing assessment notification");
    echo "Notification logic executed. Check logs/laravel.log for results.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
