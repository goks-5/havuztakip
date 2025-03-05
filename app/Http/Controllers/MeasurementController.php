<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MeasurementController extends Controller
{
    public function getSumForTag(Request $request) {
        $deviceId = $request->device_id;
        $originalTagId = $request->tag_id;  // Örn: 3
    
        // Başına 10 eklenmiş yeni tag_id
        $newTagId = '10' . $originalTagId;  // Örn: "103"
    
        $start = $request->start_date;
        $end = $request->end_date;
    
        $sum = DB::table('device_datas')
            ->where('device_id', $deviceId)
            ->where('data_id', $newTagId)
            ->whereBetween('created_at', [$start, $end])
            ->sum('value');
    
        return response()->json(['sum' => $sum]);
    }
    
}
