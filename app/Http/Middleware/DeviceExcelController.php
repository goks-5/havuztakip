<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Company;
use App\Device;
use App\DeviceData;
use Carbon\Carbon;

class DeviceExcelController extends Controller
{
    /**
     * Excel için canlı cihaz listesi endpoint’i
     * - Header’da Authorization: Bearer <token>
     * - Ya da ?company_token=<token>
     */
    public function index(Request $request)
    {
        // 1) Header’dan Bearer XYZ formatını veya raw Authorization’ı al
        $header = $request->header('Authorization', '');
        if (str_starts_with($header, 'Bearer ')) {
            $token = substr($header, 7);
        } else {
            $token = $header;
        }

        // 2) Header boşsa query-string’den al
        if (! $token) {
            $token = $request->query('company_token');
        }

        // 3) Token kontrolü
        if (! $token || strlen($token) < 4) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'missing authorization',
                'timestamp' => time(),
                'date'      => date('Y-m-d H:i:s'),
            ], 400);
        }

        // 4) Geçerli bir şirket mi?
        $company = Company::where('token', $token)->first();
        if (! $company) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'token not validate',
                'timestamp' => time(),
                'date'      => date('Y-m-d H:i:s'),
            ], 403);
        }

        // 5) Cihazları company_id’ya göre al
        $devices = Device::where('company_id', $company->id)
                    ->get(['id','device_id','name','tags','created_at']);

        // 6) JSON olarak geri gönder
        return response()->json($devices);
    }

     public function data(Request $request, $id, $dataId)
    {
        // --- 1) Token kontrolü (index()’teki ile aynı) ---
        $header = $request->header('Authorization', '');
        if (str_starts_with($header, 'Bearer ')) {
            $token = substr($header, 7);
        } else {
            $token = $header;
        }
        if (! $token) {
            $token = $request->query('company_token');
        }
        $company = Company::where('token', $token)->firstOrFail();

        // --- 2) Cihazı al ---
        $device = Device::where('company_id', $company->id)
                        ->findOrFail($id);

        // --- 3) DeviceData modelini kullanarak data_id verilerini çek ---
        $rows = DeviceData::where('device_id', $device->id)
                  ->where('data_id', $dataId)      // artık data_id sütununa bakıyor
                  ->orderBy('created_at')
                  ->get();

        return response()->json($rows);
    }

    public function dailyLatest(Request $request)
    {
        // Token kontrolü (copy-paste from index)
        $header = $request->header('Authorization', '');
        $token = str_starts_with($header, 'Bearer ') ? substr($header, 7) : $header;
        if (! $token) $token = $request->query('company_token');
        $company = Company::where('token', $token)->firstOrFail();

        // Cihazları al
        $devices = Device::where('company_id', $company->id)->get();

        $result = [];

        foreach ($devices as $device) {
            // Tags json'unu decode et
            $tags = json_decode($device->tags, true);

            foreach ($tags as $dataId => $label) {
                // Sadece günlük olanları al (200-299 arası)
                if ((int)$dataId >= 200 && (int)$dataId < 300) {
                    $latest = DeviceData::where('device_id', $device->id)
                                        ->where('data_id', $dataId)
                                        ->orderBy('created_at', 'desc')
                                        ->skip(1)
                                        ->first();

                    if ($latest) {
                        $result[] = [
                            'device_id'   => $device->device_id,
                            'device_name' => $device->name,
                            'data_id'     => $dataId,
                            'label'       => $label,
                            'value'       => $latest->value ?? null,
                            'created_at'  => $latest->created_at,
                        ];
                    }
                }
            }
        }

        return response()->json($result);
    }

    public function dailyByDate(Request $request)
    {
        // --- 1) Token kontrolü (dailyLatest’ten kopya) ---
        $header = $request->header('Authorization', '');
        $token  = str_starts_with($header, 'Bearer ')
                    ? substr($header, 7)
                    : $header;
        if (! $token) {
            $token = $request->query('company_token');
        }
        $company = Company::where('token', $token)->firstOrFail();

        // --- 2) Tarih parametresi ---
        $dateParam = $request->query('date', date('Y-m-d'));
        try {
            $date = Carbon::createFromFormat('Y-m-d', $dateParam);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'geçersiz date formatı (YYYY-MM-DD olmalı)',
            ], 400);
        }
        $start = $date->copy()->startOfDay();
        $end   = $date->copy()->endOfDay();

        // --- 3) Cihazları al ve filtreli verileri çek ---
        $devices = Device::where('company_id', $company->id)->get();
        $result  = [];

        foreach ($devices as $device) {
            $tags = json_decode($device->tags, true);
            foreach ($tags as $dataId => $label) {
                $id = (int)$dataId;
                if ($id >= 200 && $id < 300) {
                    $record = DeviceData::where('device_id', $device->id)
                        ->where('data_id', $dataId)
                        ->whereBetween('created_at', [$start, $end])
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($record) {
                        $result[] = [
                            'device_id'   => $device->device_id,
                            'device_name' => $device->name,
                            'data_id'     => $dataId,
                            'label'       => $label,
                            'value'       => $record->value,
                            'created_at' => $record->created_at,
                        ];
                    }
                }
            }
        }

        return response()->json($result);
    }

}
