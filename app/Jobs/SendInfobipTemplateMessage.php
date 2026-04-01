<?php

namespace App\Jobs;

use App\Services\InfobipService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendInfobipTemplateMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $recipients; // E.164 numbers
    public string $templateName;
    public string $language;
    public array $placeholders;

    public function __construct(array $recipients, string $templateName, string $language = 'en', array $placeholders = [])
    {
        $this->recipients = $recipients;
        $this->templateName = $templateName;
        $this->language = $language;
        $this->placeholders = $placeholders;
    }

    public function handle(InfobipService $infobip): void
    {
        foreach ($this->recipients as $to) {
            try {
                $infobip->sendWhatsAppTemplateDetailed($to, $this->templateName, $this->language, $this->placeholders);
            } catch (\Throwable $e) {
                // Avoid breaking caller flow; report for visibility
                report($e);
            }
        }
    }
}