<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notifications {type} {to?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Referral and Escalation email notifications';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $type = $this->argument('type');
        $to = $this->argument('to') ?: 'fjatakalula@powertel.co.zw';
        
        $this->info("Testing notification type: {$type}");
        $this->info("Sending to: {$to}");

        try {
            if ($type === 'referral') {
                $this->testReferral($to);
            } elseif ($type === 'escalation') {
                $this->testEscalation($to);
            } else {
                $this->error("Invalid type. Use 'referral' or 'escalation'.");
                return Command::FAILURE;
            }

            $this->info('Notification test completed successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to send notification.');
            $this->error('Error: ' . $e->getMessage());
            Log::error("Notification Test Failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    protected function testReferral($to)
    {
        $data = [
            'fault_ref' => 'REF-TEST-001',
            'customer' => 'Test Enterprise Ltd',
            'service_type' => 'Dedicated Fiber',
            'from_section' => 'NOC',
            'to_section' => 'Access Network',
            'referred_by' => 'Test User',
            'referred_at' => now()->toDateTimeString(),
            'remark' => 'Testing referral email template and logic.',
        ];

        Mail::send('emails.fault_referred', $data, function ($message) use ($to) {
            $message->to($to)
                    ->subject("TEST: Fault Referred to Your Section: REF-TEST-001");
        });
    }

    protected function testEscalation($to)
    {
        $data = [
            'fault_ref' => 'ESC-TEST-002',
            'customer' => 'Test Residential',
            'service_type' => 'Home Fiber',
            'escalation_type' => 'Manager Escalation',
            'escalated_at' => now()->toDateTimeString(),
        ];

        Mail::send('emails.fault_escalated', $data, function ($message) use ($to) {
            $message->to($to)
                    ->subject("TEST: Fault Escalated: ESC-TEST-002 (Manager Escalation)");
        });
    }
}
