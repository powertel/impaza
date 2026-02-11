<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EwsService
{
    /**
     * Send an email via Exchange Web Services (EWS).
     *
     * @param string $to
     * @param string $subject
     * @param string $body
     * @return bool
     */
    public function sendEmail(string $to, string $subject, string $body): bool
    {
        $url = env('EWS_URL');
        $username = env('EWS_USERNAME');
        $password = env('EWS_PASSWORD');
        $domain = env('EWS_DOMAIN');

        if (!$url || !$username || !$password) {
            Log::error('EWS: Configuration missing in .env');
            return false;
        }

        if ($domain) {
            // If a domain is provided, use the DOMAIN\user format
            // Strip domain if already present and strip email suffix (e.g., alerts@powertel.co.zw -> alerts)
            $cleanUsername = str_replace("{$domain}\\", '', $username);
            $cleanUsername = explode('@', $cleanUsername)[0];
            $fullUsername = "{$domain}\\{$cleanUsername}";
        } else {
            $fullUsername = $username;
        }

        $escapedSubject = htmlspecialchars($subject, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $escapedBody = htmlspecialchars($body, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
               xmlns:m="http://schemas.microsoft.com/exchange/services/2006/messages" 
               xmlns:t="http://schemas.microsoft.com/exchange/services/2006/types" 
               xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header>
    <t:RequestServerVersion Version="Exchange2013" />
  </soap:Header>
  <soap:Body>
    <m:CreateItem MessageDisposition="SendOnly">
      <m:Items>
        <t:Message>
          <t:Subject>{$escapedSubject}</t:Subject>
          <t:Body BodyType="HTML">{$escapedBody}</t:Body>
          <t:ToRecipients>
            <t:Mailbox>
              <t:EmailAddress>{$to}</t:EmailAddress>
            </t:Mailbox>
          </t:ToRecipients>
        </t:Message>
      </m:Items>
    </m:CreateItem>
  </soap:Body>
</soap:Envelope>
XML;

        try {
            Log::debug('EWS: Attempting to send email', [
                'url' => $url,
                'username' => $fullUsername,
                'to' => $to
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'Accept' => 'text/xml',
                'SOAPAction' => 'http://schemas.microsoft.com/exchange/services/2006/messages/CreateItem',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ])
            ->withOptions([
                'curl' => [
                    CURLOPT_HTTPAUTH => CURLAUTH_ANY,
                    CURLOPT_USERPWD => "{$fullUsername}:{$password}",
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_CONNECTTIMEOUT => 15,
                    CURLOPT_TIMEOUT => 60,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS => 5,
                ],
            ])
            ->send('POST', $url, [
                'body' => $xml,
            ]);

            if ($response->successful()) {
                Log::info("EWS: Email sent successfully to {$to}", ['subject' => $subject]);
                return true;
            } else {
                Log::error("EWS: Failed to send email to {$to}", [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("EWS: Connection error: " . $e->getMessage(), [
                'to' => $to,
                'subject' => $subject,
            ]);
            return false;
        }
    }
}
