<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Device;
use App\DeviceData;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\Cast\Double;

use Illuminate\Support\Facades\Log;
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
        $response = ['status'=>'','message'=>'','date' => date('Y-m-d H:i:s'),'timestamp'=>time()];
        $timestamp = strtotime($device->updated_at) + $device->period;
        $changeTags = array_filter(json_decode($device->tags === NULL ? '{}' : $device->tags, true), function ($k) {
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

        $multiplier = json_decode($device->multiplier, true);
        $offset = json_decode($device->offset, true);

        // fix data
        
        
        foreach ($parameters['data'] as $key => $value) {

            if (isset($offset[$key]) && !empty($offset[$key])) {
                $offsetValue = floatval($offset[$key]);
            } else {
                $offsetValue = 0; 
            }
            if (isset($multiplier[$key]) && !empty($multiplier[$key])) {
                $multiplierValue = floatval($multiplier[$key]);
            } else {
                $multiplierValue = 1; 
            }
            $parameters['data'][$key] = ($value  * $multiplierValue ) +  $offsetValue;
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
            $response['message'] = "last data saved";
            $device->timestamps = false;
        }

        if ($device->token == null) {
            $token = setting('device.default_token',null); 
            if(empty($token)){
                $token = Str::random(32);
            }
            $device->token = $token;
            $response['message'] = "new token generate";
            $response['token'] = $device->token;
        }

        if(!empty($parameters['productDetail'])){
            $device->product_detail =  $parameters['productDetail'];   
        }

        if(!empty($parameters['fieldDetail'])){
            $device->field_detail =  $parameters['fieldDetail'];   
            $key = Cache::get('device_field_details_' . $device->id);
            $key[date('Y-m-d H:i')] = $parameters['fieldDetail'];
            Cache::put('device_field_details_' . $device->id, $key, Carbon::now()->addDays(2));
        }
 

        $device->last_data = json_encode($replace, true);
        $device->tags_last_change = json_encode($changeTags, true);
        $device->last_at =  date('Y-m-d H:i:s');
        $device->save();
        $response['status'] = 'success';


        return response()->json($response, 200);
    }

    public function devices(Request $request)
    {
        $company = $request->get('company');
        $devices = Device::where('company_id', $company)->get();

        foreach ($devices as $key => $device) {
            $returndevice['device_id'] = $device->device_id;
            $returndevice['company_id'] = $device->company_id;
            $returndevice['name'] = $device->name;
            $returndevice['last_at'] = $device->last_at;
            $returndevice['status'] =  'success';
            $returndevice['date'] =  date('Y-m-d H:i:s');
            $returndevice['timestamp'] =  time();
            $tags = json_decode($device->tags, true);
            $last_data = json_decode($device->last_data, true);
            $returndevice['tags'] = array();
            foreach ($tags as $key2 => $tag) {
                $data['name'] = $tag;
                $data['value'] = $last_data[$key2];
                $returndevice['tags'][$key2] =  (object)$data;
            }
            $return[$key] = (object)$returndevice;
        }
        return $return;
    }


    public function device(Request $request, $device_id)
    {
        $company = $request->get('company');
        $device = Device::where(['device_id' => $device_id, 'company_id' => $company])->first();

        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'do not have permission', 'date' => date('Y-m-d H:i:s'), 'timestamp' => time()], 403);
        } else {
            $returndevice['device_id'] = $device->device_id;
            $returndevice['company_id'] = $device->company_id;
            $returndevice['name'] = $device->name;
            $returndevice['last_at'] = $device->last_at;
            $returndevice['status'] =  'success';
            $returndevice['date'] =  date('Y-m-d H:i:s');
            $returndevice['timestamp'] =  time();
            $tags = json_decode($device->tags, true);
            $last_data = json_decode($device->last_data, true);
            $returndevice['tags'] = array();
            foreach ($tags as $key2 => $tag) {
                $data['name'] = $tag;
                $data['value'] = $last_data[$key2] ?? null;
                $returndevice['tags'][$key2] =  (object)$data;
            }
            return $returndevice;
        }
    }

    public function tag(Request $request, $device_id, $index)
    {
        $company = $request->get('company');
        $device = Device::where(['device_id' => $device_id, 'company_id' => $company])->first();

        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'do not have permission', 'date' => date('Y-m-d H:i:s'), 'timestamp' => time()], 403);
        } else {
            $returndevice['status'] =  'success';
            $returndevice['date'] =  date('Y-m-d H:i:s');
            $returndevice['timestamp'] =  time();
            $tags = json_decode($device->tags, true);
            $last_data = json_decode($device->last_data, true);
            if (isset($tags[$index])) {
                $returndevice['name'] = $tags[$index];
                $returndevice['value'] = $last_data[$index];
                return  $returndevice;
            } else {
                return response()->json(['status' => 'error', 'message' => 'device index not found',  'date' => date('Y-m-d H:i:s'), 'timestamp' => time()], 404);
            }
        }
    }


    public function writeTag(Request $request, $device_id, $index,$value)
    {
        $company = $request->get('company');
        $device = Device::where(['mac'=>'00:00:00:00:00:02','device_id' => $device_id, 'company_id' => $company])->first();

        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'do not have permission', 'date' => date('Y-m-d H:i:s'), 'timestamp' => time()], 403);
        } else {
            $returndevice['status'] =  'write';
            $returndevice['date'] =  date('Y-m-d H:i:s');
            $returndevice['timestamp'] =  time();
            $tags = json_decode($device->tags, true);
            $last_data = json_decode($device->last_data, true);
            if (isset($tags[$index])) {
                $last_data[$index] = $value;
                $device->last_data = json_encode($last_data, true);
                $device->last_at =  date('Y-m-d H:i:s');
                $device->save();
                $saveData[] = [
                    'device_id' => $device->id,
                    'data_id' => $index,
                    'value' => $value,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                DeviceData::insert($saveData);
                $returndevice['name'] = $tags[$index];
                $returndevice['value'] = $last_data[$index];
                return  $returndevice;
            } else {
                return response()->json(['status' => 'error', 'message' => 'device index not found',  'date' => date('Y-m-d H:i:s'), 'timestamp' => time()], 404);
            }
        }
    }

    public function tests(Request $request)
    {
        $response['method'] = $request->method();
        $response['headers'] = $request->header();
        $response['request'] = $request->all();
        return response()->json($response,200);
    }
}
