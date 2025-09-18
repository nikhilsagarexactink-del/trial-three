<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class VimeoService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.vimeo.com/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('VIMEO_ACCESS_TOKEN'),
                'Accept' => 'application/json',
            ],
        ]);
    }

    public static function getVideoDetails($videoId)
    {
        $url = "https://api.vimeo.com/videos/{$videoId}";
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('VIMEO_ACCESS_TOKEN'),
        ])->get($url);

        if ($response->successful()) {
            return $response->json(); // Returns the decoded JSON as an associative array
        }

        return [
            'success' => false,
            'message' => $response->body(), // Return the error response body
        ];
    }
    public static function findAllVimeoVideos($request)
    {
        $post = $request->all();
        $url = "https://api.vimeo.com/me/videos";
        $perPage = 50; // Load 50 per request

        $params = [
            'per_page' => $perPage,
            'page' => 1,
        ];

        if (!empty($post['search'])) {
            $params['query'] = $post['search']; // Add search filter
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('VIMEO_ACCESS_TOKEN'),
        ])->get($url, $params);

        if ($response->successful()) {
            $videos = $response->json();
            return response()->json($videos['data']); // Return only 'data' array
        }

        return response()->json(['error' => 'Failed to fetch videos'], 500);
    }

}
