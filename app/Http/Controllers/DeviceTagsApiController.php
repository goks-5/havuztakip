<?php

namespace App\Http\Controllers;

use App\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeviceTagsApiController extends Controller
{

    public function seriesByDeviceIdTag(Request $request, $device_id, $tag_id, $date = null)
    {
        DB::disableQueryLog();

        try {
            // 1) Cihaz
            $dev = Device::where('device_id', $device_id)
                ->first(['id', 'device_id', 'name', 'tags']);

            if (!$dev) {
                return response()->json(['message' => 'device not found'], 404);
            }

            // 2) tag_id doğrulama (0–999)
            $tagId = (int) $tag_id;
            if ($tagId < 0 || $tagId > 999) {
                return response()->json(['message' => 'tag_id out of range (0-999)'], 422);
            }

            // 3) TAG ADI (cihaz.tags JSON'undan çöz)
            $tagName = null;
            $tagsJson = json_decode($dev->tags, true) ?: [];
            if (!empty($tagsJson)) {
                $map = [];
                foreach ($tagsJson as $k => $v) {
                    if (is_numeric($k)) {
                        $d = (int) $k;
                        if ($d >= 0 && $d <= 999) {
                            $map[$d] = is_string($v) ? $v
                                : (is_scalar($v) ? (string) $v : json_encode($v, JSON_UNESCAPED_UNICODE));
                        }
                    } elseif (is_string($v) && preg_match('/^\s*(\d+)\D*(.*)$/u', $v, $m)) {
                        $d = (int) $m[1];
                        $name = trim($m[2]) !== '' ? trim($m[2]) : $v;
                        if ($d >= 0 && $d <= 999) {
                            $map[$d] = $name;
                        }
                    }
                }
                if (array_key_exists($tagId, $map)) {
                    $tagName = $map[$tagId];
                }
            }

            // 4) Tarih modu
            $series = [];

            if ($date !== null && $date !== '') {
                // ---- Günün tamamı ----
                $tz    = config('app.timezone', 'UTC');
                $dt    = \DateTime::createFromFormat('Y-m-d', $date, new \DateTimeZone($tz));
                $errs  = \DateTime::getLastErrors();
                $valid = $dt && !$errs['warning_count'] && !$errs['error_count'] && $dt->format('Y-m-d') === $date;

                if (!$valid) {
                    return response()->json(['message' => 'invalid date format, use YYYY-MM-DD'], 422);
                }

                $from = Carbon::instance($dt)->startOfDay();
                $to   = (clone $from)->addDay(); // [from, to)

                // Limit (yalnız tarihli modda anlamlı)
                $limit = (int) ($request->query('limit', 20000));
                $limit = min(max($limit, 1000), 100000);

                // Önce PK id ile dene
                $rows = DB::table('device_datas')
                    ->select('value', 'created_at')
                    ->where('device_id', $dev->id)       // çoğu şemada PK
                    ->where('data_id', $tagId)
                    ->where('created_at', '>=', $from->format('Y-m-d H:i:s'))
                    ->where('created_at', '<',  $to->format('Y-m-d H:i:s'))
                    ->orderBy('created_at', 'asc')
                    ->limit($limit)
                    ->get();

                // Fallback: device_datas.device_id iş-ID ise
                if ($rows->isEmpty()) {
                    $rows = DB::table('device_datas')
                        ->select('value', 'created_at')
                        ->where('device_id', $dev->device_id)
                        ->where('data_id', $tagId)
                        ->where('created_at', '>=', $from->format('Y-m-d H:i:s'))
                        ->where('created_at', '<',  $to->format('Y-m-d H:i:s'))
                        ->orderBy('created_at', 'asc')
                        ->limit($limit)
                        ->get();
                }

                foreach ($rows as $r) {
                    $series[] = [
                        'value'      => $r->value,
                        'created_at' => $r->created_at instanceof \DateTimeInterface
                            ? $r->created_at->getTimestamp()
                            : (int) strtotime((string) $r->created_at),
                    ];
                }
            } else {
                // ---- SON KAYIT ----
                $row = DB::table('device_datas')
                    ->select('value', 'created_at')
                    ->where('device_id', $dev->id)
                    ->where('data_id', $tagId)
                    ->orderBy('created_at', 'desc')
                    ->limit(1)
                    ->first();

                if (!$row) {
                    $row = DB::table('device_datas')
                        ->select('value', 'created_at')
                        ->where('device_id', $dev->device_id)
                        ->where('data_id', $tagId)
                        ->orderBy('created_at', 'desc')
                        ->limit(1)
                        ->first();
                }

                if ($row) {
                    $series[] = [
                        'value'      => $row->value,
                        'created_at' => $row->created_at instanceof \DateTimeInterface
                            ? $row->created_at->getTimestamp()
                            : (int) strtotime((string) $row->created_at),
                    ];
                }
            }

            // 5) Çıkış
            return response()->json([
                'device_id' => $dev->device_id,
                'name'      => $dev->name,
                'tag_name'  => $tagName,
                'tag'       => $series, // tarih yoksa tek elemanlı dizi (son değer)
            ], 200);
        } catch (\Throwable $e) {
            Log::error('[seriesByDeviceIdTag] '.$e->getMessage(), [
                'device_id' => $device_id,
                'tag_id'    => $tag_id,
                'date'      => $date,
            ]);
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    /**
     * İki tarih arasındaki ilk ve son değer farkını (Tüketim) hesaplar.
     * URL: /enerji/read/tag_value_cost/{device_id}/{tag_id}/{date1}/{date2}
     */
    public function costBetweenDates(Request $request, $device_id, $tag_id, $date1, $date2)
    {
        // Log performans için kapalı
        DB::disableQueryLog();

        try {
            // --- 1) Cihazı Bul ---
            $dev = Device::where('device_id', $device_id)
                         ->first(['id', 'device_id', 'name', 'tags']);

            if (!$dev) {
                return response()->json(['message' => 'device not found'], 404);
            }

            // --- 2) Tag ID Kontrolü ---
            $tagId = (int) $tag_id;
            // Tag ismini bulma (Mevcut yapını korudum)
            $tagName = null;
            $tagsJson = json_decode($dev->tags, true) ?: [];
            if (!empty($tagsJson)) {
                $map = [];
                foreach ($tagsJson as $k => $v) {
                    // Basit format: "1": "İsim"
                    if (is_numeric($k)) {
                        $d = (int)$k;
                        if ($d >= 0 && $d <= 999) {
                            $map[$d] = is_string($v) ? $v : json_encode($v, JSON_UNESCAPED_UNICODE);
                        }
                    } 
                    // Karmaşık format: "sensor1": "1 İsim"
                    elseif (is_string($v) && preg_match('/^\s*(\d+)\D*(.*)$/u', $v, $m)) {
                        $d = (int)$m[1];
                        $name = trim($m[2]) !== '' ? trim($m[2]) : $v;
                        if ($d >= 0 && $d <= 999) {
                            $map[$d] = $name;
                        }
                    }
                }
                if (array_key_exists($tagId, $map)) {
                    $tagName = $map[$tagId];
                }
            }

            // --- 3) Tarih Formatlama ---
            try {
                $startDate = Carbon::createFromFormat('Y-m-d', $date1)->startOfDay(); // date1 00:00:00
                $endDate   = Carbon::createFromFormat('Y-m-d', $date2)->endOfDay();   // date2 23:59:59
            } catch (\Exception $e) {
                return response()->json(['message' => 'Invalid date format. Use YYYY-MM-DD'], 422);
            }

            // --- 4) İlk ve Son Veriyi Çekme ---
            
            // Hangi ID sütununu kullanacağız? (Önce PK, sonra String ID)
            // Bu kontrolü sorgu içinde yapmak yerine bir kez belirleyelim:
            // (Not: İki ayrı sorgu atmak performans açısından burası için daha sağlıklıdır)
            
            // A) Aralıktaki İLK Kayıt (En eski)
            $firstRecord = DB::table('device_datas')
                ->select('value', 'created_at')
                ->where('device_id', $dev->id) // Önce PK dene
                ->where('data_id', $tagId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'asc') // Eskiden yeniye
                ->first();

            // Eğer PK ile bulamazsa String ID ile dene (Fallback)
            if (!$firstRecord) {
                $firstRecord = DB::table('device_datas')
                    ->select('value', 'created_at')
                    ->where('device_id', $dev->device_id)
                    ->where('data_id', $tagId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'asc')
                    ->first();
            }

            // B) Aralıktaki SON Kayıt (En yeni)
            // Sadece ilk kayıt varsa, son kayıt da odur diyemeyiz, tekrar sorgulamalıyız.
            if ($firstRecord) {
                 $lastRecord = DB::table('device_datas')
                    ->select('value', 'created_at')
                    ->where('device_id', $dev->id) // Önce PK
                    ->where('data_id', $tagId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'desc') // Yeniden eskiye
                    ->first();
                
                 // Fallback
                 if (!$lastRecord) {
                     $lastRecord = DB::table('device_datas')
                        ->select('value', 'created_at')
                        ->where('device_id', $dev->device_id)
                        ->where('data_id', $tagId)
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->orderBy('created_at', 'desc')
                        ->first();
                 }
            } else {
                $lastRecord = null;
            }

            // --- 5) Hesaplama ---
            $firstValue = $firstRecord ? (float)$firstRecord->value : 0;
            $lastValue  = $lastRecord ? (float)$lastRecord->value : 0;
            
            // Fark (Tüketim)
            // Eğer sadece 1 veri varsa fark 0 olur.
            $consumption = ($lastRecord && $firstRecord) ? ($lastValue - $firstValue) : 0;

            // Negatif çıkma ihtimaline karşı (Sayaç sıfırlanması vs.) kontrol istersen:
            // if ($consumption < 0) $consumption = 0; // Opsiyonel

            return response()->json([
                'device_id'   => $dev->device_id,
                'name'        => $dev->name,
                'tag_name'    => $tagName,
                'start_date'  => $startDate->toDateTimeString(),
                'end_date'    => $endDate->toDateTimeString(),
                'data' => [
                    'first_value'    => $firstValue,
                    'first_read_at'  => $firstRecord ? $firstRecord->created_at : null,
                    'last_value'     => $lastValue,
                    'last_read_at'   => $lastRecord ? $lastRecord->created_at : null,
                    'difference'     => round($consumption, 3), // 3 haneye yuvarla
                ]
            ], 200);

        } catch (\Throwable $e) {
            Log::error('[costBetweenDates] ' . $e->getMessage(), [
                'device_id' => $device_id,
                'tag_id' => $tag_id
            ]);
            return response()->json(['message' => 'Server Error'], 500);
        }
    }
}
