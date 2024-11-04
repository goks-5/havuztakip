<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DeviceData;
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
}
