<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Http;

class TwilioService
{
    protected $client;

    public function __construct()
    {
        if (isProduction()) {
            $this->client = new Client(
                env('TWILIO_SID'),
                env('TWILIO_AUTH_TOKEN')
            );
        } else {
            $this->client = null;
            Log::info('Twilio client not initialized - Non-production environment');
        }
    }


    public function sendSms($to, $message)
    {
        return $this->client->messages->create(
            $to,
            [
                // 'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => htmlspecialchars($message),
                "messagingServiceSid" => env('TWILIO_MASSAGE_SERVICE_ID')
            ]
        );
    }

    public static function getEmailStats($startDate, $endDate, $broadcastId, $metrics)
    {
        try {
            $url = 'https://api.sendgrid.com/v3/categories/stats';
            $broadcastCategory = 'broadcast_' . $broadcastId;
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.env('SENDGRID_API_KEY'),
            ])->get($url, [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'categories' => $broadcastCategory,
                'aggregated_by' => 'day', // Options: 'day', 'week', 'month'
            ]);
            $emails = $response->json();
            return $emails;
        } catch (\Exception $e) {
            // Handle exceptions, log errors, etc.
            throw new \Exception('Error: ' . $e->getMessage());
        }
    }
}
