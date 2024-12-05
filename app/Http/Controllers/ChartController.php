<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DeviceData;
use App\Device;
use Illuminate\Support\Facades\Log;

class ChartController extends Controller
{
    public function getFilteredData(Request $request)
    {
        try {
            // Retrieve query parameters
            $deviceId = $request->query('device_id');
            $dataId = $request->query('data_id');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            // Log received parameters for debugging
            Log::info("Fetching data for device: $deviceId, data: $dataId, start_date: $startDate, end_date: $endDate");

            // Validate parameters
            if (!$deviceId || !$dataId || !$startDate || !$endDate) {
                Log::warning("Invalid parameters received");
                return response()->json(['error' => 'Invalid parameters'], 400);
            }

            // Ensure dates are in the correct format (Y-m-d H:i:s)
            if (!strtotime($startDate) || !strtotime($endDate)) {
                Log::warning("Invalid date format received for start_date or end_date");
                return response()->json(['error' => 'Invalid date format'], 400);
            }

            // Fetch data based on filters, only retrieving necessary fields
            $data = DeviceData::where('device_id', $deviceId)
                ->where('data_id', $dataId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at')
                ->get(['created_at', 'value']);

            // Check if data is empty
            if ($data->isEmpty()) {
                Log::info("No data found for device_id: $deviceId, data_id: $dataId, within dates $startDate - $endDate");
                return response()->json(['message' => 'No data found for the selected criteria'], 404);
            }

            Log::info("Data successfully retrieved for device_id: $deviceId, data_id: $dataId");
            return response()->json(['data' => $data]);

        } catch (\Exception $e) {
            // Log any exception for debugging
            Log::error("Error fetching chart data: " . $e->getMessage());
            Log::error($e->getTraceAsString()); // Detailed trace for debugging
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function getResourceTotals(Request $request)
    {
        try {
            // Retrieve query parameters
            $fieldNames = $request->query('field_names'); // Expecting a comma-separated list of field names
            // $startDate = $request->query('start_date');
            // $endDate = $request->query('end_date');
    
            // Validate parameters
            // if (!$fieldNames || !$startDate || !$endDate) {
            //     Log::warning("Invalid parameters received");
            //     return response()->json(['error' => 'Invalid parameters'], 400);
            // }
    
            // Ensure dates are in the correct format
            // if (!strtotime($startDate) || !strtotime($endDate)) {
            //     Log::warning("Invalid date format received");
            //     return response()->json(['error' => 'Invalid date format'], 400);
            // }
    
            // Convert field names into an array
            $fieldArray = explode(',', $fieldNames);
    
            // Initialize totals for each category
            $totals = [
                'electricity' => 0,
                'water' => 0,
                'natural_gas' => 0,
                'meterage' => 0,
            ];

            // Get today's date
            $today = now()->startOfDay();
    
            foreach ($fieldArray as $fieldName) {
                // Fetch devices matching the current field_name
                $devices = Device::where('field_name', $fieldName)->get(['id', 'resource_type']);
    
                foreach ($devices as $device) {
                    $deviceId = $device->id;
                    $resourceType = $device->resource_type;
    
                    // Determine the category based on resource_type
                    if ($resourceType === 'elektrik') {
                        $category = 'electricity';
                    } elseif ($resourceType === 'baraj_su' || $resourceType === 'sanayi_su') {
                        $category = 'water';
                    } elseif ($resourceType === 'dogalgaz') {
                        $category = 'natural_gas';
                    } elseif ($resourceType === 'metraj') {
                        $category = 'meterage';
                    } else {
                        continue; // Skip unknown resource types
                    }
    
                    // Sum the value for device_datas with data_id = 100
                    $totalValue = DeviceData::where('device_id', $deviceId)
                        ->where('data_id', 200)
                        ->whereDate('created_at', $today) // Only today's data
                        ->sum('value');
    
                    // Add the total to the corresponding category
                    $totals[$category] += $totalValue;
                }
            }
    
            // Check if all totals are zero
            if (array_sum($totals) === 0) {
                Log::info("No data found for the selected criteria");
                return response()->json(['message' => 'No data found for the selected criteria'], 404);
            }
    
            Log::info("Resource totals successfully calculated");
            return response()->json(['totals' => $totals]);
    
        } catch (\Exception $e) {
            // Log any exception for debugging
            Log::error("Error calculating resource totals: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Server error'], 500);
        }
    }
    
}