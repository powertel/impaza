<?php

namespace App\Jobs;

use App\Services\InfobipService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendInfobipMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $recipients; // E.164 numbers
    public string $text;

    public function __construct(array $recipients, string $text)
    {
        $this->recipients = $recipients;
        $this->text = $text;
    }

    public function handle(InfobipService $infobip): void
    {
        foreach ($this->recipients as $to) {
            try {
                $infobip->sendWhatsAppText($to, $this->text);
            } catch (\Throwable $e) {
                // Swallow errors to avoid breaking lifecycle; consider logging
                report($e);
            }
        }
    }
}