<?php

namespace App\Repositories;

use App\Models\Header;

class HeaderTextRepository
{
    public static function saveHeaderText($request)
    {
        try {
            $post = $request->all();
            $keys = [
                'getting-started',
                'fitness-profile',
                'health-tracker',
                'water-tracker',
                'motivation',
                'training-library',
                'speed',
                'journal',
                'step-counter',
                'recipes',
                'activity-tracker',
                'my-rewards',
                'how-to-earn-rewards',
                'sleep-tracker',
                'dashboard-widget',
            ];
            foreach ($post as $key => $value) {
                $header = Header::where('module_key', $key)->first();
                if (! empty($header)) {
                    if (in_array($key, $keys)) {
                        $header->description = $value;
                        $header->save();
                    }
                }
            }

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function getHeaders($keys = [])
    {
        try {
            $headerArr = [];
            if (! empty($keys)) {
                $headerKeys = [];
                foreach ($keys as $key) {
                    $headerKeys[$key] = '';
                }
            } else {
                $headerKeys = [
                    'getting-started' => '',
                    'fitness-profile' => '',
                    'health-tracker' => '',
                    'water-tracker' => '',
                    'motivation' => '',
                    'training-library' => '',
                    'speed' => '',
                    'journal' => '',
                    'step-counter' => '',
                    'recipes' => '',
                    'activity-tracker' => '',
                    'my-rewards' => '',
                    'how-to-earn-rewards' => '',
                    'sleep-tracker' => '',
                    'dashboard-widget'=> '',
                ];
            }

            $headers = Header::where('status', '!=', 'deleted')->get();
            if (! empty($headers)) {
                foreach ($headers as $key => $data) {
                    if (count($keys) == 0 || in_array($data['module_key'], $keys)) {
                        $headerArr[$data['module_key']] = $data['description'];
                    }
                }
            }
            $headerValues = [];
            foreach ($headerKeys as $key => $param) {
                $headerValues[$key] = $headerArr[$key];
            }

            return $headerValues;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function getHeaderText($request)
    {
        try {
            $header = Header::where('module_key', $request['type'])->first();

            return $header;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
