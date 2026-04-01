<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private function normalizePhone(string $n): string
    {
        $n = trim($n);
        $n = preg_replace('/[\s\-\.]/', '', $n);
        if (strpos($n, '+') !== 0) {
            $n = '+' . ltrim($n, '+');
        }
        return $n;
    }

    public function send(array $recipients, string $message): bool
    {
        $url = rtrim(env('POWERTEL_SMS_API_URL', 'https://sms.powertel.co.zw/api/send_sms'), '/');
        $apiKey = env('POWERTEL_SMS_API_KEY','76f94de53729467795b70479de96639e');

        if (empty($recipients) || !$message) {
            return false;
        }

        $to = array_map(function($x) { return $this->normalizePhone((string)$x); }, array_values($recipients));
        $payload = [
            'to' => $to,
            'message' => $message,
        ];
        if (!empty($apiKey)) {
            $payload['api_key'] = $apiKey;
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        if (!empty($apiKey)) {
            $headers['X-API-KEY'] = $apiKey;
            $headers['Api-Key'] = $apiKey;
            $headers['Authorization'] = 'Bearer ' . $apiKey;
        }

        try {
            $response = Http::withHeaders($headers)->timeout(15)->post($url, $payload);
        } catch (\Throwable $e) {
            Log::error('SMS connection error', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return false;
        }

        $success = $response->successful();
        if ($success) {
            Log::info('SMS sent', [
                'to_count' => count($to),
                'status' => $response->status(),
            ]);
        } else {
            Log::error('SMS send failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
        return $success;
    }
}
