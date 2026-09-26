<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TextBeeService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.textbee.base_url');
        $this->apiKey = config('services.textbee.api_key');
    }

    public function sendSms(
        string $mobileNumber,
        string $message
    ): array {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post(
            $this->baseUrl . '/gateway/send-sms',
            [
                'recipients' => [$mobileNumber],
                'message' => $message,
            ]
        );

        $response->throw();

        return $response->json();
    }
}
