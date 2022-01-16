<?php

namespace App\Http\Middleware;

use App\Device;
use App\UndefineDevice;
use Closure;
use Illuminate\Support\Facades\Log;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $parameters = $request->all();

        if (!isset($parameters['mac']) || !isset($parameters['device_id']) || !isset($parameters['data'])) {
            Log::error("missing parameters",$parameters);
            return response()->json(['status' => 'error', 'message' => 'missing parameters', 'timestamp' => time() , 'date' => date('Y-m-d H:i:s') ], 400);
        }
        $device = Device::where(['mac' => $parameters['mac'], 'device_id' => $parameters['device_id']])->first();
        if ($device) {
            if ($this->validateData($parameters, $device)) {
                return $next($request);
            } else {
                Log::error("data not validate",$parameters);
                return response()->json(['status' => 'error', 'message' => 'data not validate', 'timestamp' => time(), 'date' => date('Y-m-d H:i:s') ], 403);
            }
        } else {
            $uDevice = UndefineDevice::firstOrNew(['mac' => $parameters['mac'], 'device_id' => $parameters['device_id']]);
            $uDevice->save();
            Log::warning("device not found",$parameters);
            return response()->json(['status' => 'warning', 'message' => 'device not found', 'timestamp' => time(), 'date' => date('Y-m-d H:i:s') ], 404);
        }
    }

    private function validateData(array $parameters, Device $device): bool
    {
        if ($device->token == null) {
            return true;
        } elseif (!isset($parameters['verifyToken'])) {
            return false;
        } else {
            $hashText = "";
            foreach ($parameters['data'] as $index => $value) {
                $hashText .= $index . "_" . $value . "|";
            }
            $hashText .= $device->token;
            return $parameters['verifyToken'] == hash("md5", $hashText);
        }
    }
}
