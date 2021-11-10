<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Device;
use App\DeviceData;
use Illuminate\Support\Str;
class Api extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function write(Request $request)
    {
        $parameters = $request->all();
        $device = Device::where(['mac' => $parameters['mac'], 'device_id' => $parameters['device_id']])->first();
        $response = [ 'date' => date('Y-m-d H:i:s') ];
        $timestamp = strtotime($device->updated_at) + $device->period;
        $changeTags = array_filter(json_decode($device->tags === NULL ? '{}' :$device->tags, true), function ($k) {
            return $k >= '1000';
        }, ARRAY_FILTER_USE_KEY);
        $changeAt = json_decode($device->tags_last_change, true);
        if (!is_array($changeAt)) {
            $changeAt = array();
        }
        $changeTags = array_replace($changeTags, $changeAt);
        $old = json_decode($device->last_data, true);
        if (!is_array($old)) {
            $old = array();
        }
        $replace = array_replace($old, $parameters['data']);
        ksort($replace);
        foreach ($parameters['data'] as $key => $value) {
            if (isset($changeTags[$key + 1000]) && isset($old[$key]) && $old[$key] != $value)
                $changeTags[$key + 1000] = date('Y-m-d H:i:s');
        }
        if ($timestamp <= time()) {
            $saveData = [];
            foreach ($parameters['data'] as $key => $value) {

                $saveData[] = [
                    'device_id' => $device->id,
                    'data_id' => $key,
                    'value' => $value,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }
            DeviceData::insert($saveData);
            $response['message'] = "device data saved";
        } else {
            $response['message'] = "device data saved only to last";
            $device->timestamps = false;
        }

        if ($device->token == null) {
            $device->token = Str::random(32);
            $response['message'] = "new token generate";
            $response['token'] = $device->token;
        }

        $device->last_data = addslashes(json_encode($replace, true));
        $device->tags_last_change = addslashes(json_encode($changeTags, true));
        $device->last_at =  date('Y-m-d H:i:s');
        $device->save();
        $response['status'] = 'success';
        $response['timestamp'] = time();


        return response()->json( $response, 200);
    }
}
