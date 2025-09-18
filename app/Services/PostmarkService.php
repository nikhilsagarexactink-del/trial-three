<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Http;

class PostmarkService
{
    protected $client;
    
    public function __construct()
    {
        $this->client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    public static function getEmailStats($startDate, $endDate, $broadcastId)
    {
        try {
            // get Sent,Bounced,Opens,TotalClicks
            $url = 'https://api.postmarkapp.com/stats/outbound';
            $broadcastTag = 'broadcast_' . $broadcastId;
            $response = Http::withHeaders([
                'X-Postmark-Server-Token' => env('POSTMARK_TOKEN'),
            ])->get($url, [
                'fromdate' => $startDate,
                'todate' => $endDate,
                'tag' => $broadcastTag,
            ]);
            // Fetch data from /stats/outbound/sends
            $sendsUrl = 'https://api.postmarkapp.com/stats/outbound/tracked';
            $sendsResponse = Http::withHeaders([
                'X-Postmark-Server-Token' => env('POSTMARK_TOKEN'),
            ])->get($sendsUrl, [
                'fromdate' => $startDate,
                'todate' => $endDate,
                'tag' => $broadcastTag,
            ]);
            $stats = $response->json();
            $allSend = $sendsResponse->json();
            // Return only the specific fields you want
            return [
                'sent' => $stats['Sent'] ?? 0,
                'total_sent' => $allSend['Tracked'] ?? 0,
                'bounced' => $stats['Bounced'] ?? 0,
                'opens' => $stats['Opens'] ?? 0,
                'unique_opens' => $stats['UniqueOpens'] ?? 0,
                'clicks' => $stats['TotalClicks'] ?? 0,
            ];
        } catch (\Exception $e) {
            // Handle exceptions, log errors, etc.
            throw new \Exception('Error: ' . $e->getMessage());
        }
    }

    public static function getSenderSignature()
    {
        try {
            $url = 'https://api.postmarkapp.com/senders';
            $response = Http::withHeaders([
                'X-Postmark-Account-Token' => env('POSTMARK_API_KEY'), // Use Account Token, not Server Token
                'Accept' => 'application/json',
            ])->get($url,[
                'count' => 50, // Add count parameter
                'offset' => 0  // Optional: Set offset for pagination
            ]);
            if ($response->successful()) {
                return $response->json();
            }
            throw new \Exception('Postmark API error: ' . $response->body());
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            throw new \Exception('Error: ' . $e->getMessage());
        }
    }

}
