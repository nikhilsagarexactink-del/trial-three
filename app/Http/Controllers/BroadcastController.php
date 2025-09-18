<?php

namespace App\Http\Controllers;

use App\Http\Requests\BroadcastRequest;
use App\Repositories\BroadcastRepository;
use App\Repositories\SettingRepository;
use App\Services\SmsService;
use Config;
use File;
use Illuminate\Http\Request;
use SendGrid;
use View;

class BroadcastController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Show broadcast index page.
     *
     * @return Redirect to broadcast index page
     */
    public function index()
    {
        try {
            return view('broadcast.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show add form.
     *
     * @return Redirect to add form
     */
    public function addForm(Request $request)
    {
        try {
            $settings = SettingRepository::getSettings();
            $timezone = '';
            if (! empty($settings['timezone'])) {
                $contents = File::get(base_path('public/assets/timezones.json'));
                $timezoneArr = json_decode(json: $contents, associative: true);
                foreach ($timezoneArr as $tz) {
                    if ($settings['timezone'] == $tz['zone']) {
                        $timezone = $tz['name'];
                    }
                }
            }
            // For Clone Action
            if (! empty($request['id'])) {
                $result = BroadcastRepository::findOne(['id' => $request['id']]);

                return view('broadcast.add', compact('result', 'timezone'));
            } else {
                return view('broadcast.add', compact('timezone'));
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Save Broadcast message
     *
     * @return Json
     */
    public function saveBroadcast(BroadcastRequest $request)
    {
        try {
            $result = BroadcastRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Broadcast successfully created.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    /**
     * Get broadcast data
     *
     * @return Json,Html
     */
    public function loadBroadcastList(Request $request)
    {
        try {
            $userType = userType();
            $result = BroadcastRepository::loadBroadcastList($request);
            $view = View::make('broadcast._list', ['data' => $result, 'userType' => $userType])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination],
                    'message' => '',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    /**
     * Show edit form
     *
     * @return Json,Html
     */
    public function editForm(Request $request)
    {
        try {
            $result = BroadcastRepository::findOne(['id' => $request->id]);
            if (! empty($result)) {
                $settings = SettingRepository::getSettings();
                $timezone = '';
                if (! empty($settings['timezone'])) {
                    $contents = File::get(base_path('public/assets/timezones.json'));
                    $timezoneArr = json_decode(json: $contents, associative: true);
                    foreach ($timezoneArr as $tz) {
                        if ($settings['timezone'] == $tz['zone']) {
                            $timezone = $tz['name'];
                        }
                    }
                }

                return view('broadcast.edit', compact('result', 'timezone'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Update Broadcast
     *
     * @return Json
     */
    public function updateBroadcast(BroadcastRequest $request)
    {
        try {
            $result = BroadcastRepository::update($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Broadcast successfully created.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    /**
     * Change Status
     *
     * @return Json
     */
    public function changeStatus(Request $request)
    {
        try {
            $result = BroadcastRepository::changeStatus($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => $request->status == 'deleted' ? 'Record successfully deleted.' : 'Status successfully updated.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    /**
     * Broadcast message cron
     *
     * @return Json
     */
    public function broadcastMessageCron()
    {
        try {
            $results = BroadcastRepository::broadcastMessageCron();

            return response()->json(
                [
                    'success' => true,
                    'data' => $results,
                    'message' => 'Broadcast message.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    /**
     * Get broadcast statics
     *
     * @return Json
     */
    public function fetchBroadcastStatics(Request $request)
    {
        try {
            $results = BroadcastRepository::fetchBroadcastStatics($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $results,
                    'message' => 'Broadcast statics fetch successfully.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    // public function fetchEmailStatistics($broadcastID=18)
    // {
    //     try {
    //         // Initialize SendGrid client with your API key
    //         $sendgrid = new SendGrid(env('SENDGRID_API_KEY'));

    //         // Setup your query parameters, e.g., start and end time
    //         $queryParams = http_build_query([
    //             'start_time' => strtotime('-30 days'),
    //             'end_time' => strtotime('now'),
    //             'limit' => 100, // Adjust the limit as needed
    //         ]);

    //         // Set the endpoint for fetching messages
    //         $endpoint = "/v3/messages";

    //         // Make the GET request to SendGrid
    //         $response = $sendgrid->client->request('GET', $endpoint, ['query' => $queryParams]);
    //         dd($response);

    //         // Check if the response status code is 200 (OK)
    //         if ($response->statusCode() !== 200) {
    //             throw new \Exception('Failed to fetch email statistics. Status Code: ' . $response->statusCode());
    //         }

    //         // Access the response body (JSON string)
    //         $body = $response->body();
    //         // Decode the JSON string into a PHP array
    //         $decodedBody = json_decode($body, true);
    //         dd($decodedBody);

    //         // Check for JSON decoding errors
    //         if (json_last_error() !== JSON_ERROR_NONE) {
    //             throw new \Exception('Failed to decode JSON: ' . json_last_error_msg());
    //         }

    //         // Filter messages based on the custom argument 'broadcastID'
    //         $statistics = [];
    //         foreach ($decodedBody['messages'] as $message) {
    //             if (isset($message['custom_args']['broadcastID']) && $message['custom_args']['broadcastID'] === $broadcastID) {
    //                 $statistics[] = $message;
    //             }
    //         }

    //         return $statistics; // Return the filtered statistics

    //     } catch (\Exception $e) {
    //         // Handle exceptions, log errors, etc.
    //         throw new \Exception('Error: ' . $e->getMessage());
    //     }
    // }

    /**
     * Fetch email statics
     *
     * @return Json
     */
    public function fetchEmailStatistics(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $broadcastId = 1;

        // Metrics to fetch
        $metrics = ['requests', 'delivered', 'opens', 'clicks', 'bounces'];
        // Get specific email stats
        $emailStats = $this->smsService->getEmailStats($startDate, $endDate, $broadcastId, $metrics);
        // dd($emailStats);
        // Filter emails based on custom `broadcastId` header
        // $filteredEvents = array_filter($emailStats, function ($event) use ($broadcastId) {
            //     return isset($event['X-Custom-Arg']['broadcastId']) &&
            //            $event['X-Custom-Arg']['broadcastId'] == (string)$broadcastId;
        // });
        $stats = [];
        foreach ($emailStats as $event) {
            if (isset($event['stats'][0]['metrics'])) {
                $eventMetrics = $event['stats'][0]['metrics'];

                foreach ($metrics as $metric) {
                    $stats[$metric] = $stats[$metric] ?? 0;

                    if (isset($eventMetrics[$metric])) {
                        $stats[$metric] += $eventMetrics[$metric];
                    }
                }
            }
        }
        dd($stats);

        return $stats;
    }

    /**
     * Webhook
     *
     * @return Json
     */
    public function handleWebhook(Request $request)
    {
        $events = $request->all();
        dd($events);
        foreach ($events as $event) {
            $broadcastID = $event['custom_args']['broadcastID'] ?? null;

            if ($broadcastID) {
                // Process events, e.g., store in the database
            }
        }

        return response()->json(['status' => 'success']);
    }
    public function removeBroadcastAlert(Request $request)
    {
        try {
            $result = BroadcastRepository::removeBroadcastAlert($request->id);
            return response()->json(
                [
                    'success' => true,
                    'data' => [], 
                    'message' => 'Broadcast Alert Successfully removed.'
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => [], 
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }
}
