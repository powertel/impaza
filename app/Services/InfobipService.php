<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InfobipService
{
    public function sendWhatsAppText(string $toE164, string $text): bool
    {
        $base = rtrim(config('services.infobip.base_url') ?? env('INFOBIP_BASE_URL', 'https://api.infobip.com'), '/');
        $apiKey = config('services.infobip.api_key') ?? env('INFOBIP_API_KEY');
        $from = config('services.infobip.whatsapp_number') ?? env('INFOBIP_WHATSAPP_NUMBER');

        if (!$apiKey || !$from) {
            Log::error('Infobip: Missing configuration for WhatsApp send', [
                'has_api_key' => (bool)$apiKey,
                'has_from' => (bool)$from,
                'used_env_fallback' => (config('services.infobip.api_key') === null || config('services.infobip.whatsapp_number') === null),
                'base_url' => $base,
            ]);
            return false;
        }

        $to = $this->normalizePhone($toE164);
        $endpoint = $base . '/whatsapp/1/message/text';
        $payload = [
            'from' => ltrim($from, '+'), // Infobip typically expects MSISDN without '+'
            'to' => $to,
            'content' => [
                'text' => $text,
            ],
        ];

        Log::info('Infobip: Request payload (text)', [
            'endpoint' => $endpoint,
            'from' => $payload['from'],
            'to' => $payload['to'],
            'text_len' => strlen($text),
            'text_preview' => mb_substr($text, 0, 160),
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'App ' . $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($endpoint, $payload);

        $success = $response->successful();
        if ($success) {
            Log::info('Infobip: WhatsApp message sent', [
                'to' => $payload['to'],
                'from' => $payload['from'],
                'status' => $response->status(),
            ]);
        } else {
            Log::error('Infobip: WhatsApp message failed', [
                'to' => $payload['to'],
                'from' => $payload['from'],
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
        return $success;
    }

    public function sendWhatsAppTextDetailed(string $toE164, string $text): array
    {
        $base = rtrim(config('services.infobip.base_url') ?? env('INFOBIP_BASE_URL', 'https://api.infobip.com'), '/');
        $apiKey = config('services.infobip.api_key') ?? env('INFOBIP_API_KEY');
        $from = config('services.infobip.whatsapp_number') ?? env('INFOBIP_WHATSAPP_NUMBER');

        if (!$apiKey || !$from) {
            Log::error('Infobip: Missing configuration for WhatsApp send', [
                'has_api_key' => (bool)$apiKey,
                'has_from' => (bool)$from,
                'used_env_fallback' => (config('services.infobip.api_key') === null || config('services.infobip.whatsapp_number') === null),
                'base_url' => $base,
            ]);
            return [
                'success' => false,
                'status' => 0,
                'body' => 'Missing Infobip configuration: api_key or whatsapp_number'
            ];
        }

        $to = $this->normalizePhone($toE164);
        $endpoint = $base . '/whatsapp/1/message/text';
        $payload = [
            'from' => ltrim($from, '+'),
            'to' => $to,
            'content' => [
                'text' => $text,
            ],
        ];

        Log::info('Infobip: Request payload (text detailed)', [
            'endpoint' => $endpoint,
            'from' => $payload['from'],
            'to' => $payload['to'],
            'text_len' => strlen($text),
            'text_preview' => mb_substr($text, 0, 160),
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'App ' . $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($endpoint, $payload);

        $body = null;
        try {
            $body = $response->json();
        } catch (\Throwable $e) {
            $body = $response->body();
        }

        $success = $response->successful();
        if ($success) {
            Log::info('Infobip: Text WhatsApp message sent', [
                'to' => $payload['to'],
                'from' => $payload['from'],
                'status' => $response->status(),
            ]);
        } else {
            Log::error('Infobip: Text WhatsApp message failed', [
                'to' => $payload['to'],
                'from' => $payload['from'],
                'status' => $response->status(),
                'body' => is_string($body) ? $body : json_encode($body),
            ]);
        }

        return [
            'success' => $success,
            'status' => $response->status(),
            'body' => $body,
        ];
    }

    public function sendWhatsAppTemplateDetailed(string $toE164, string $templateName, string $language = 'en', array $placeholders = []): array
    {
        $base = rtrim(config('services.infobip.base_url') ?? env('INFOBIP_BASE_URL', 'https://api.infobip.com'), '/');
        $apiKey = config('services.infobip.api_key') ?? env('INFOBIP_API_KEY');
        $from = config('services.infobip.whatsapp_number') ?? env('INFOBIP_WHATSAPP_NUMBER');

        if (!$apiKey || !$from) {
            Log::error('Infobip: Missing configuration for template WhatsApp send', [
                'has_api_key' => (bool)$apiKey,
                'has_from' => (bool)$from,
                'used_env_fallback' => (config('services.infobip.api_key') === null || config('services.infobip.whatsapp_number') === null),
                'base_url' => $base,
            ]);
            return [
                'success' => false,
                'status' => 0,
                'body' => 'Missing Infobip configuration: api_key or whatsapp_number'
            ];
        }

        $to = $this->normalizePhone($toE164);
        $endpoint = $base . '/whatsapp/1/message/template';

        $content = [
            'templateName' => $templateName,
            'language' => $language,
        ];
        if (!empty($placeholders)) {
            $content['templateData'] = [
                'body' => [
                    'placeholders' => array_values($placeholders),
                ],
            ];
        }

        $payload = [
            'from' => ltrim($from, '+'),
            'to' => $to,
            'content' => $content,
        ];

        Log::info('Infobip: Request payload (template)', [
            'endpoint' => $endpoint,
            'from' => $payload['from'],
            'to' => $payload['to'],
            'templateName' => $templateName,
            'language' => $language,
            'placeholders_count' => count($placeholders),
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'App ' . $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($endpoint, $payload);

        $body = null;
        try {
            $body = $response->json();
        } catch (\Throwable $e) {
            $body = $response->body();
        }

        $success = $response->successful();
        if ($success) {
            Log::info('Infobip: Template WhatsApp message sent', [
                'to' => $payload['to'],
                'from' => $payload['from'],
                'status' => $response->status(),
            ]);
        } else {
            Log::error('Infobip: Template WhatsApp message failed', [
                'to' => $payload['to'],
                'from' => $payload['from'],
                'status' => $response->status(),
                'body' => is_string($body) ? $body : json_encode($body),
            ]);
        }

        return [
            'success' => $success,
            'status' => $response->status(),
            'body' => $body,
        ];
    }

    public function getWhatsAppMessageStatus(string $messageId): array
    {
        $base = rtrim(config('services.infobip.base_url') ?? env('INFOBIP_BASE_URL', 'https://api.infobip.com'), '/');
        $apiKey = config('services.infobip.api_key') ?? env('INFOBIP_API_KEY');

        if (!$apiKey) {
            Log::error('Infobip: Missing API key for status query', [
                'has_api_key' => (bool)$apiKey,
                'base_url' => $base,
            ]);
            return [
                'success' => false,
                'status' => 0,
                'body' => 'Missing Infobip API key'
            ];
        }

        $primaryEndpoint = $base . '/whatsapp/1/message/logs';
        $alternateEndpoint = $base . '/whatsapp/1/message/statuses';
        $endpoint = $primaryEndpoint;
        Log::info('Infobip: Status query', [
            'endpoint' => $endpoint,
            'messageId' => $messageId,
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'App ' . $apiKey,
            'Accept' => 'application/json',
        ])->get($endpoint, [
            'messageId' => $messageId,
        ]);

        // Fallback to alternate endpoint if primary returns 404
        if ($response->status() === 404) {
            $endpoint = $alternateEndpoint;
            Log::warning('Infobip: Primary logs endpoint 404, retrying alternate', [
                'retry_endpoint' => $endpoint,
            ]);
            $response = Http::withHeaders([
                'Authorization' => 'App ' . $apiKey,
                'Accept' => 'application/json',
            ])->get($endpoint, [
                'messageId' => $messageId,
            ]);
        }

        $body = null;
        try {
            $body = $response->json();
        } catch (\Throwable $e) {
            $body = $response->body();
        }

        if ($response->successful()) {
            Log::info('Infobip: Status query success', [
                'status' => $response->status(),
                'endpoint_used' => $endpoint,
            ]);
        } else {
            Log::error('Infobip: Status query failed', [
                'status' => $response->status(),
                'endpoint_used' => $endpoint,
                'body' => is_string($body) ? $body : json_encode($body),
            ]);
        }

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'body' => $body,
        ];
    }

    private function normalizePhone(string $raw): string
    {
        $trimmed = trim($raw);
        if ($trimmed === '') {
            return $raw;
        }
        $num = preg_replace('/[^0-9+]/', '', $trimmed);
        if ($num === '') {
            return $raw;
        }
        if ($num[0] === '+') {
            return $num;
        }
        if (substr($num, 0, 2) === '00') {
            return '+' . substr($num, 2);
        }
        $default = config('services.infobip.default_country_code') ?? env('INFOBIP_DEFAULT_COUNTRY_CODE');
        $defaultDigits = $default ? preg_replace('/[^0-9]/', '', $default) : null;
        if (substr($num, 0, 1) === '0' && $defaultDigits) {
            return '+' . $defaultDigits . substr($num, 1);
        }
        if (preg_match('/^[1-9][0-9]{6,}$/', $num)) {
            return '+' . $num;
        }
        return $raw;
    }
}