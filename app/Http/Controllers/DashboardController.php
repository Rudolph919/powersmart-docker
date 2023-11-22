<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        //Gets the allowance/usage for API calls
        $allowance = $this->fetchAllowanceStatus();

        if (!Cache::has('loadSheddingSchedule')) {
            $loadSheddingSchedule = $this->fetchLoadSheddingSchedule();

            Cache::put('loadSheddingSchedule', $loadSheddingSchedule, now()->addMinutes(60));
        } else {
            $loadSheddingSchedule = Cache::get('loadSheddingSchedule');
        }

        //Gets the suggested meal prep times to avoid loadshedding
        $mealRecommendations = $this->processLoadSheddingSchedule($loadSheddingSchedule);

        if ($request->input('area_search') == 'true') {
            $searchTerm = $request->input('searchInput');

            // Check if the results are in the cache
            if (Cache::has($searchTerm)) {
                $areas = Cache::get($searchTerm);
            } else {
                // If not in cache, make the API call
                $areas = $this->doAreaSearchByText($searchTerm);

                // Store the results in the cache for, for example, 60 minutes (adjust as needed)
                Cache::put($searchTerm, $areas, now()->addMinutes(60));
            }
        } else {
            $areas = null;
        }


        if ($request->input('area_information') == 'true') {
            $searchId = $request->input('searchId');

            // Check if the results are in the cache
            if (Cache::has($searchId)) {
                $areaInfo = Cache::get($searchId);
            } else {
                // If not in cache, make the API call
                $areaInfo = $this->doAreaInfoSearchById($searchId);

                // Store the results in the cache for, for example, 60 minutes (adjust as needed)
                Cache::put($searchId, $areaInfo, now()->addMinutes(60));
            }
        } else {
            $areaInfo = null;
        }

        return view('dashboard', [
            'allowance' => $allowance,
            'mealRecommendations' => $mealRecommendations,
            'areas' => $areas,
            'areaInfo' => $areaInfo
        ]);
    }


    public function fetchAllowanceStatus()
    {
        $endpoint = 'https://developer.sepush.co.za/business/2.0/api_allowance';

        $data = $this->curlCommandAction($endpoint);

        $allowanceData = json_decode($data, true);

        if (!isset($allowanceData['error'])) {
            $count = $allowanceData['allowance']['count'];
            $limit = $allowanceData['allowance']['limit'];
            $type = $allowanceData['allowance']['type'];
            $percentageUsed = ($count / $limit) * 100;
            $allowanceData['allowance']['percentage'] = $percentageUsed;
            $allowanceData['allowance']['limit_type'] = $type;
        }

        return $allowanceData;
    }


    public function fetchLoadSheddingSchedule()
    {
        $endpoint = 'https://developer.sepush.co.za/business/2.0/status';

        $data = $this->curlCommandAction($endpoint);

        return json_decode($data);
    }


    public function doAreaSearchByText($searchTerm)
    {
        $endpoint = 'https://developer.sepush.co.za/business/2.0/areas_search?text=' . $searchTerm;

        $data = $this->curlCommandAction($endpoint);

        return json_decode($data, true, 512);
    }

    public function doAreaInfoSearchById($areaId)
    {
        $endpoint = 'https://developer.sepush.co.za/business/2.0/area?id=' . $areaId;

        $data = $this->curlCommandAction($endpoint);

        return json_decode($data, true, 512);
    }


    public function curlCommandAction($endpoint)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Token: ' . env('ESKOM_API_KEY'),
        ));

        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function processLoadSheddingSchedule($loadSheddingSchedule)
    {
        // Initialize an empty array to store meal recommendations
        $mealRecommendations = [];

        // Get the current time
        $currentTime = Carbon::now();

        // Check if there are any upcoming load shedding stages for Cape Town
        if (count($loadSheddingSchedule->status->capetown->next_stages) === 0) {
            // No upcoming stages for Cape Town
            $mealRecommendations['Cape Town'][] = [
                'stage' => $loadSheddingSchedule->status->capetown->stage,
                'message' => 'No upcoming load shedding stages for Cape Town. Current stage: ' . $loadSheddingSchedule->status->capetown->stage,
            ];
        } else {
            // Process upcoming load shedding stages for Cape Town
            foreach ($loadSheddingSchedule->status->capetown->next_stages as $stageData) {

                $stageStartTime = Carbon::parse($stageData->stage_start_timestamp);

                // Check if the next load shedding stage is within 30 minutes of the current time
                if ($stageStartTime->diffInMinutes($currentTime) <= 30) {
                    // Avoid suggesting meal preparation during the next load shedding stage
                    continue;
                }

                // Calculate the time window for optimal meal preparation
                $optimalStartTime = $stageStartTime->clone()->subMinutes(30);
                $optimalEndTime = $stageStartTime->clone()->subMinutes(5);

                // Create a meal recommendation for the identified time window
                $mealRecommendation = [
                    'stage' => $stageData->stage,
                    'optimalStartTime' => $optimalStartTime->toDateTimeString(),
                    'optimalEndTime' => $optimalEndTime->toDateTimeString(),
                ];

                // Add the meal recommendation to the array
                $mealRecommendations['Cape Town'][] = $mealRecommendation;
            }
        }

        // Check if there are any upcoming load shedding stages for Eskom
        if (count($loadSheddingSchedule->status->eskom->next_stages) === 0) {
            // No upcoming stages Nationally
            $mealRecommendations['Nationally'][] = [
                'stage' => $loadSheddingSchedule->status->eskom->stage,
                'message' => 'No upcoming load shedding stages for Eskom. Current stage: ' . $loadSheddingSchedule->status->eskom->stage,
            ];
        } else {
            // Process upcoming load shedding stages for Eskom
            foreach ($loadSheddingSchedule->status->eskom->next_stages as $stageData) {

                $stageStartTime = Carbon::parse($stageData->stage_start_timestamp);

                // Check if the next load shedding stage is within 30 minutes of the current time
                if ($stageStartTime->diffInMinutes($currentTime) <= 30) {
                    // Avoid suggesting meal preparation during the next load shedding stage
                    continue;
                }

                // Calculate the time window for optimal meal preparation
                $optimalStartTime = $stageStartTime->clone()->subMinutes(30);
                $optimalEndTime = $stageStartTime->clone()->subMinutes(5);

                // Create a meal recommendation for the identified time window
                $mealRecommendation = [
                    'stage' => $stageData->stage,
                    'optimalStartTime' => $optimalStartTime->toDateTimeString(),
                    'optimalEndTime' => $optimalEndTime->toDateTimeString(),
                ];

                // Add the meal recommendation to the array
                $mealRecommendations['Nationally'][] = $mealRecommendation;
            }
        }

        return $mealRecommendations;
    }
}
