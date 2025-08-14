<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Device;
use Carbon\Carbon;

class DeviceTagsApiController extends Controller
{
    public function index(Request $request)
    {
        DB::disableQueryLog(); // uzun sorgularda bellek şişmesin

        // 0) Şirket
        $companyId = $request->attributes->get('company') ?: $request->get('company');
        if (!$companyId) {
            return response()->json([
                'status' => 'error',
                'message' => 'company not resolved',
                'date' => date('Y-m-d H:i:s'),
                'timestamp' => time(),
            ], 400);
        }

        // 1) Tek tarih zorunlu
        $dateStr = $request->query('date');
        if (!$dateStr) {
            return response()->json([
                'status' => 'error',
                'message' => "date is required (use YYYY-MM-DD)",
                'date' => date('Y-m-d H:i:s'),
                'timestamp' => time(),
            ], 422);
        }
        try {
            $start = Carbon::createFromFormat('Y-m-d', $dateStr)->startOfDay();
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => "invalid date format for 'date' (use YYYY-MM-DD)",
                'date' => date('Y-m-d H:i:s'),
                'timestamp' => time(),
            ], 422);
        }
        $endEx = $start->copy()->addDay()->startOfDay(); // end dahil için exclusive

        // 2) Cihazlar (+ opsiyonel tek cihaz filtresi)
        $devicePk = $request->query('device_pk');
        $deviceId = $request->query('device');

        $q = Device::where('company_id', $companyId);
        if ($devicePk !== null && $devicePk !== '') $q->where('id', (int)$devicePk);
        elseif ($deviceId !== null && $deviceId !== '') $q->where('device_id', $deviceId);

        $devices = $q->get(['id','device_id','name','tags']);
        if ($devices->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'no devices',
                'data' => [],
                'date' => date('Y-m-d H:i:s'),
                'timestamp' => time(),
            ], 200);
        }

        $deviceIds  = $devices->pluck('id')->all();
        $deviceInfo = [];
        $tagMapByPk = [];
        foreach ($devices as $dev) {
            $deviceInfo[$dev->id] = ['device_id' => $dev->device_id, 'device_name' => $dev->name];
            $map = [];
            $tags = json_decode($dev->tags, true) ?: [];
            foreach ($tags as $k => $v) {
                if (is_numeric($k)) {
                    $d = (int)$k;
                    if ($d >= 0 && $d <= 99) $map[$d] = is_string($v) ? $v : (is_scalar($v) ? (string)$v : json_encode($v, JSON_UNESCAPED_UNICODE));
                } elseif (is_string($v) && preg_match('/^\s*(\d+)\D*(.*)$/u', $v, $m)) {
                    $d = (int)$m[1]; $name = trim($m[2]) !== '' ? trim($m[2]) : $v;
                    if ($d >= 0 && $d <= 99) $map[$d] = $name;
                }
            }
            $tagMapByPk[$dev->id] = $map;
        }

        // 3) Çıkış modu
        $flatParam = strtolower((string)($request->query('flat', '0')));
        $isFlat = in_array($flatParam, ['1','true','yes','on'], true) || $request->query('format') === 'rows';
        $limit = (int)($request->query('limit', 0));
        if ($isFlat) {
            if ($limit <= 0) $limit = 20000;                 // varsayılan üst limit
            $limit = min(max($limit, 1000), 100000);         // güvenlik bandı
        }

        // 4) Sorgular
        $flatRows  = [];
        $latestMap = [];
        $chunkSize = ($devicePk || $deviceId) ? 1 : 50; // tek cihazda küçük chunk yeter

        foreach (array_chunk($deviceIds, $chunkSize) as $chunk) {
            $ph = implode(',', array_fill(0, count($chunk), '?'));

            if ($isFlat) {
                // O GÜNÜN TÜM SATIRLARI (performanslı RANGE filtresi)
                $sql = "
                    SELECT device_id, data_id, value, created_at
                    FROM device_datas
                    WHERE device_id IN ($ph)
                      AND data_id BETWEEN 0 AND 99
                      AND created_at >= ? AND created_at < ?
                    ORDER BY device_id ASC, data_id ASC, created_at ASC
                    LIMIT {$limit}
                ";
                $params = array_merge($chunk, [$start->format('Y-m-d H:i:s'), $endEx->format('Y-m-d H:i:s')]);
            } else {
                // NESTED: her (device_id,data_id) için o günün SON kaydı — tek atışta
                $sql = "
                    SELECT dd.device_id, dd.data_id, dd.value, dd.created_at
                    FROM device_datas dd
                    INNER JOIN (
                        SELECT device_id, data_id, MAX(created_at) AS max_created
                        FROM device_datas
                        WHERE device_id IN ($ph)
                          AND data_id BETWEEN 0 AND 99
                          AND created_at >= ? AND created_at < ?
                        GROUP BY device_id, data_id
                    ) m
                      ON m.device_id = dd.device_id
                     AND m.data_id   = dd.data_id
                     AND m.max_created = dd.created_at
                    ORDER BY dd.device_id ASC, dd.data_id ASC
                ";
                $params = array_merge($chunk, [$start->format('Y-m-d H:i:s'), $endEx->format('Y-m-d H:i:s')]);
            }

            try {
                $rows = DB::select($sql, $params);
            } catch (\Throwable $e) {
                @file_put_contents('/tmp/devicetagsapi.log',
                    '[DeviceTagsApi SQL error] '.$e->getMessage().' date='.$dateStr.PHP_EOL,
                    FILE_APPEND
                );
                return response()->json([
                    'status' => 'error',
                    'message' => 'database error',
                    'date' => date('Y-m-d H:i:s'),
                    'timestamp' => time(),
                ], 500);
            }

            foreach ($rows as $r) {
                $pk     = (int)$r->device_id;
                $dataId = (int)$r->data_id;
                if (!isset($tagMapByPk[$pk][$dataId])) continue;

                if ($isFlat) {
                    $name = $tagMapByPk[$pk][$dataId];
                    $flatRows[] = [
                        'device_pk'     => $pk,
                        'device_id'     => $deviceInfo[$pk]['device_id'],
                        'device_name'   => $deviceInfo[$pk]['device_name'],
                        'data_id'       => $dataId,
                        'name'          => $name,
                        'resource_type' => $this->guessResourceType($name),
                        'value'         => $r->value,
                        'created_at'    => $r->created_at,
                    ];
                } else {
                    $latestMap[$pk][$dataId] = ['value' => $r->value, 'created_at' => $r->created_at];
                }
            }
        }

        // 5) Çıkış
        if ($isFlat) {
            usort($flatRows, fn($a,$b) => [$a['device_id'],$a['data_id'],$a['created_at']]
                                        <=> [$b['device_id'],$b['data_id'],$b['created_at']]);
            return response()->json([
                'status'    => 'success',
                'message'   => 'ok',
                'data'      => $flatRows,
                'date'      => date('Y-m-d H:i:s'),
                'timestamp' => time(),
            ], 200);
        }

        $nested = [];
        foreach ($devices as $dev) {
            $pk      = $dev->id;
            $tagsOut = [];
            foreach ($tagMapByPk[$pk] as $dataId => $name) {
                $li = $latestMap[$pk][$dataId] ?? null;
                $tagsOut[] = [
                    'data_id'       => $dataId,
                    'name'          => $name,
                    'resource_type' => $this->guessResourceType($name),
                    'value'         => $li['value']     ?? null,
                    'created_at'    => $li['created_at']?? null,
                ];
            }
            $nested[] = [
                'id'        => $pk,
                'device_id' => $dev->device_id,
                'name'      => $dev->name,
                'tags'      => $tagsOut,
            ];
        }

        return response()->json([
            'status'    => 'success',
            'message'   => 'ok',
            'data'      => $nested,
            'date'      => date('Y-m-d H:i:s'),
            'timestamp' => time(),
        ], 200);
    }

    private function guessResourceType($name)
    {
        if ($name === null) return null;
        $s = mb_strtolower((string)$name, 'UTF-8');
        if (mb_stripos($s, 'doğalgaz', 0, 'UTF-8') !== false || mb_stripos($s, 'dogalgaz', 0, 'UTF-8') !== false) return 'doğalgaz';
        if (mb_stripos($s, 'elektrik', 0, 'UTF-8') !== false) return 'elektrik';
        if (mb_stripos($s, 'metraj', 0, 'UTF-8') !== false) return 'metraj';
        if (mb_stripos($s, ' su', 0, 'UTF-8') !== false || preg_match('/(^|[^a-zğüşıöç0-9])su([^a-zğüşıöç0-9]|$)/u', $s)) return 'su';
        return null;
    }
}
