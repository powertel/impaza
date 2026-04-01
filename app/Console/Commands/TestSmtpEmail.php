<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestSmtpEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:smtp-email {to?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test SMTP email sending';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $to = $this->argument('to') ?: 'fjatakalula@powertel.co.zw';
        $subject = "SMTP Test: Clearance Notification";
        $data = [
            'fault_ref' => 'TEST-2026-001',
            'customer' => 'Test Customer Residence',
            'service_type' => 'Internet Fiber',
            'rfo' => 'Power outage at base station',
            'cleared_at' => now()->toDateTimeString(),
        ];

        $this->info("Testing SMTP email connection to " . config('mail.mailers.smtp.host'));
        $this->info("Sending to: " . $to);

        try {
            Mail::send('emails.fault_cleared', $data, function ($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject);
            });
            
            $this->info('Email sent successfully via SMTP using the new template!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to send email via SMTP.');
            $this->error('Error: ' . $e->getMessage());
            Log::error("SMTP Test Failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
