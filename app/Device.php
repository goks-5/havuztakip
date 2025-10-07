<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\CompanySetting;
use App\TagAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Device extends Model
{
    use SoftDeletes;

    protected $fillable = ['token', 'last_data', 'tags', 'tags_last_change', 'last_at', 'company_id', 'product', 'hardware', 'software', 'multiplier', 'offset'];

    public static function hourly()
    {
        $sensorler = DB::table('device_last_hours')->get();
        $count = 0;

        foreach ($sensorler as $sensor) {
            $device = Device::find($sensor->device_id);

            // Eğer cihaz pasifse veri ekleme
            if (!$device || $device->status == 0) {
                Log::info("Device {$sensor->device_id} pasif, hourly çalışmadı.");
                continue;
            }

            $sonuc = Device::addhourly($sensor->device_id, $sensor->data_id, $sensor->last_hour);
            if ($sonuc) {
                $count++;
            }
        }

        Log::info("hourly tamamlandı, $count adet kayıt işlendi.");
        return $count; // <-- ARTIK null dönmeyecek
    }

    public static function fillHourly()
    {
        $devices = Device::where('mac', '<>', '00:00:00:00:00:00')
            ->where('last_at', '<', date('Y-m-d H:i:s', strtotime("-5 minute")))
            ->get();

        dump('Offline cihazlara Saatlik data giriliyor ' . count($devices) . " Adet cihaz var.");
        Log::info('Offline cihazlara Saatlik data giriliyor ' . count($devices) . " Adet cihaz var.");

        foreach ($devices as $device) {
            // Eğer cihaz pasifse veri ekleme
            if ($device->status == 0) {
                dump($device->name . " pasif, saatlik veri eklenmedi.");
                Log::info($device->name . " pasif, saatlik veri eklenmedi.");
                continue;
            }

            $lastdata = json_decode($device->last_data, true);
            $multiplier = json_decode($device->multiplier, true);
            $offset = json_decode($device->offset, true);

            if (is_array($lastdata)) {
                $tagCount = 0;
                foreach ($lastdata as $data_id => $value) {
                    if ($data_id < 100) {
                        $start = Carbon::now()->startOfHour();
                        $device_data = DeviceData::where([
                            "device_id" => $device->id,
                            "data_id"   => $data_id,
                            "hourly"    => $start
                        ])->first();

                        if (!$device_data) {
                            $offsetValue = isset($offset[$data_id]) ? floatval($offset[$data_id]) : 0;
                            $multiplierValue = isset($multiplier[$data_id]) ? floatval($multiplier[$data_id]) : 1;

                            DeviceData::insert([
                                "device_id"  => $device->id,
                                "data_id"    => $data_id,
                                "value"      => $value,
                                "created_at" => $start,
                                "hourly"     => $start,
                                "multiplier" => $multiplierValue,
                                "offset"     => $offsetValue
                            ]);
                            ++$tagCount;
                        }
                    }
                }
                dump($device->name . " offline cihazına " . $tagCount . " Etiketine Saatlik Veri Girildi");
                Log::info($device->name . " offline cihazına " . $tagCount . " Etiketine Saatlik Veri Girildi");
            }
        }
    }

    public static function virtualData()
    {
        // Hem 00:00:00:00:00:00 hem de 00:00:00:00:00:04 mac adresli cihazları sorgula
        $virtual = DB::table('devices')
        ->whereIn('mac', ['00:00:00:00:00:00', '00:00:00:00:00:04'])
        ->get();
        $devices = array();
        foreach ($virtual as $sanal) {
            $tags = json_decode($sanal->formula);
            $did = "";
            $basla = false;
            foreach ($tags as $key => $tag) {
                for ($i = 0; $i < strlen($tag); $i++) {
                    if ($tag[$i] == "_") {
                        $devices[] = $did;
                        $basla = false;
                        $did = "";
                    }
                    if ($basla) {
                        $did .= $tag[$i];
                    }
                    if ($tag[$i] == "[") {
                        $basla = true;
                    }
                }
            }
        }

        $device_data = DB::table('devices')->whereIn('id', $devices)->get();

        $degistir = array();
        foreach ($device_data as $data) {
            $lastdata = json_decode($data->last_data, true);
            if (is_array($lastdata)) {
                foreach ($lastdata as $key => $ld) {
                    $degistir["[" . $data->id . "_" . $key . "]"] = $ld;
                }
            }
        }
        foreach ($virtual as $sanal) {
            $tags = json_decode($sanal->formula);
            $last_data = json_decode($sanal->last_data, true);
            $setting = CompanySetting::select('day_start_hour', 'week_start_day', 'month_start_day')->find($sanal->company_id);
            if ($setting) {
                $setting = $setting->toArray();
            } else {
                $setting = ['day_start_hour' => 0, 'week_start_day' => 1, 'month_start_day' => 1];
            }
            foreach ($tags as $data_id => $tag) {
                foreach ($degistir as $key => $value) {
                    $tag = str_replace($key, $value, $tag);
                }

                $result = Device::calculate($tag, $setting);
                $last_data[$data_id] = (string)$result;
                DB::table('device_datas')->insert(["device_id" => $sanal->id, "data_id" => $data_id, "value" => $result, 'created_at' => date('Y-m-d H:i:s')]);
            }
            DB::table('devices')->where('id', $sanal->id)->update(['last_data' => json_encode($last_data, true), "last_at" => date('Y-m-d H:i:s')]);
        }
    }

    public static function calculate($tag, $setting)
    {
        

        $hom = (date("j") - 1) * 24 + date("G") - $setting['day_start_hour'];
        if ($hom < 0) {
            $hom = 24 +  (date("j", strtotime("-1 day")) - 1) * 24 + date("G", strtotime("-1 day")) - $setting['day_start_hour'];
        }
        $hoy = date("z") * 24 + date("G") - $setting['day_start_hour'];
        if ($hoy < 0) {
            $hoy = 24 +  date("z", strtotime("-1 day")) * 24 + date("G", strtotime("-1 day")) - $setting['day_start_hour'];
        }
        $hod = date("G") - $setting['day_start_hour'];
        if ($hod < 0) {
            $hod = 24 + $hod;
        }

        $tag = preg_replace('!pi|π!', pi(), $tag); // Replace pi with pi function
        $tag = str_replace('dom', date("j"), $tag); // day of month
        $tag = str_replace('doy', (date("z") + 1 ), $tag); // day of year
        $tag = str_replace('moy', date("n"), $tag); // month of year
        $tag = str_replace('hom', $hom, $tag); // hour of month
        $tag = str_replace('hoy', $hoy, $tag); // hour of year
        $tag = str_replace('hod', $hod, $tag); // hour of day
        $tag = preg_replace('/\s+/', '', $tag);
        
        $result = self::evalMath($tag);
        return round($result, 2);
    }

    private static function evalMath($expression)
    {
        // İzin verilen matematiksel fonksiyonlar listesi
        static $function_map = array(
            'floor'     => 'floor',
            'ceil'      => 'ceil',
            'round'     => 'round',         
            'sin'       => 'sin',
            'cos'       => 'cos',
            'tan'       => 'tan',           
            'asin'      => 'asin',
            'acos'      => 'acos',
            'atan'      => 'atan',          
            'abs'       => 'abs',
            'log'       => 'log',           
            'pi'        => 'pi',
            'exp'       => 'exp',
            'min'       => 'min',
            'max'       => 'max',
            'rand'      => 'rand',
            'fmod'      => 'fmod',
            'sqrt'      => 'sqrt',
            'deg2rad'   => 'deg2rad',
            'rad2deg'   => 'rad2deg',
        );
    
        $expression = strtolower(preg_replace('~\s+~', '', $expression));
    
        if ($expression === '') {
            return 0;
        }
    
        // İzin verilmeyen fonksiyonları kontrol et
        $expression = preg_replace_callback('~\b[a-z]\w*\b~', function($match) use($function_map) {
            $function = $match[0];
            if (!isset($function_map[$function])) {
                return '';
            }
            return $function_map[$function];
        }, $expression);
    
        // Geçersiz fonksiyon çağrılarını kontrol et
        if (preg_match('~[a-z]\w*(?![\(\w])~', $expression, $match) > 0) {
            return 0;
        }
    
        // Geçersiz karakter kontrolü
        if (preg_match('~[^-+/%*&|<>!=.()0-9a-z,]~', $expression, $match) > 0) {
            return 0;
        }
    
        // Eval işlemiyle matematiksel ifadeyi çalıştır
        try {
            return eval("return ({$expression});");
        } catch (\Throwable $th) {
            // Hata durumunda 0 döndür
            return 0;
        }
    }
    
    private static function echoTimer($start = false, $last = false)
    {
        $now = microtime(true);
        if ($start) {
            Log::info('timer : ' . ($now - $start));
            dump($now - $start);
        }
        if ($last) {
            Log::info('timer : ' . ($now - $last));
            dump($now - $last);
        }
        return $now;
    }

   public static function diffData($id = null)
    {
        $startd = false;
        $last = false;
        $startd = Device::echoTimer($startd, $last);

        if ($id == null) {
            $devices = Device::where(function ($query) {
                $query->Where('tags', 'LIKE', '% Saatlik"%')
                    ->orWhere('tags', 'LIKE', '% Günlük"%')
                    ->orWhere('tags', 'LIKE', '% Haftalık"%')
                    ->orWhere('tags', 'LIKE', '% Aylık"%')
                    ->orWhere('tags', 'LIKE', '% Yıllık"%');
            })
            ->orderBy("diff_at")
            ->limit(500)
            ->get();
        } else {
            $devices = Device::where('id', $id)->get();
        }

        dump('döngüye giriyor ' . count($devices) . " Adet cihaz var.");
        Log::info('döngüye giriyor ' . count($devices) . " Adet cihaz var.");

        foreach ($devices as $device) {
            // pasif cihazı atla
            if ($device->status == 0) {
                dump($device->name . " pasif, diffData işlenmedi.");
                Log::info($device->name . " pasif, diffData işlenmedi.");
                continue;
            }

            $device_id = $device->id;
            $last_data = json_decode($device->last_data, true);
            $types = json_decode($device->type, true);
            $setting = CompanySetting::select('day_start_hour', 'week_start_day', 'month_start_day')->find($device->company_id);
            if ($setting) {
                $setting = $setting->toArray();
            } else {
                $setting = ['day_start_hour' => 0, 'week_start_day' => 1, 'month_start_day' => 1];
            }
            $baseStart = Carbon::now()->subHours($setting['day_start_hour'])->subMinutes(40)->startOfDay()->addHours($setting['day_start_hour']);

            $deviceTags = json_decode($device->tags, true);
            $filteredData = [];

            foreach ($deviceTags as $key => $value) {
                if ($key >= 100) {
                    $lastTwoDigits = (int) substr($key, -2);
                    $newKey = 100 + $lastTwoDigits;
                    if (!in_array($newKey, $filteredData)) {
                        $filteredData[] = $newKey;
                    }
                    if (!in_array($key, $filteredData)) {
                        $filteredData[] = $key;
                    }
                }
            }
            sort($filteredData, SORT_NUMERIC);

            foreach ($filteredData as $key => $data_id) {
                $start = clone $baseStart;
                $type = 'diff';
                if ($data_id > 99 && $data_id < 200) {
                    $start = Carbon::now()->subMinutes(6)->startOfHour();
                    $end = clone $start;
                    $end->addHours(1);
                    if (isset($types[$data_id - 100])) {
                        $type = $types[$data_id - 100];
                    }
                    $last_data[$data_id]  =  Device::addDiffData($device_id, $data_id - 100, $data_id, $start->toDateTimeString(), $end->toDateTimeString(), $type);
                }
                if ($data_id > 199 && $data_id < 300) {
                    $end = clone $start;
                    $end->addHours(24);
                    if (isset($types[$data_id - 200])) {
                        $type = $types[$data_id - 200];
                    }
                    $last_data[$data_id]  =  Device::addDiffData($device_id, $data_id - 200, $data_id, $start->toDateTimeString(), $end->toDateTimeString(), $type);
                } elseif ($data_id > 299 && $data_id < 400) {
                    $start->startOfWeek($setting['week_start_day'])->addHours($setting['day_start_hour']);
                    $end = clone $start;
                    $end = $end->addDays(7);
                    if (isset($types[$data_id - 300])) {
                        $type = $types[$data_id - 300];
                    }
                    $last_data[$data_id]  =   Device::addDiffData($device_id, $data_id - 300, $data_id, $start->toDateTimeString(), $end->toDateTimeString(), $type);
                } elseif ($data_id > 399 && $data_id < 500) {
                    $start->startOfMonth()->addDays($setting['month_start_day'] - 1)->addHours($setting['day_start_hour']);
                    $end = clone $start;
                    $end = $end->endOfMonth()->addDays($setting['month_start_day'] - 1)->addHours($setting['day_start_hour']);
                    if (isset($types[$data_id - 400])) {
                        $type = $types[$data_id - 400];
                    }
                    $last_data[$data_id]  =  Device::addDiffData($device_id, $data_id - 400, $data_id, $start->toDateTimeString(), $end->toDateTimeString(), $type);
                } elseif ($data_id > 499 && $data_id < 600) {
                    $start->startOfYear()->addDays($setting['month_start_day'] - 1)->addHours($setting['day_start_hour']);
                    $end = clone $start;
                    $end = $end->endOfYear()->addDays($setting['month_start_day'] - 1)->addHours($setting['day_start_hour']);
                    if (isset($types[$data_id - 500])) {
                        $type = $types[$data_id - 500];
                    }
                    $last_data[$data_id]  =  Device::addDiffData($device_id, $data_id - 500, $data_id, $start->toDateTimeString(), $end->toDateTimeString(), $type);
                }
            }
            DB::table('devices')->where('id', $device->id)->update(['last_data' => json_encode($last_data, true), "diff_at" => date('Y-m-d H:i:s')]);
        }
        Device::echoTimer($startd, false);
    }

    private static function addDiffData($device_id, $data_id, $targetData_id, $start, $end, $type = 'diff', $default = -1)
    {
        $device = Device::find($device_id);
        if (!$device || $device->status == 0) {
            Log::info("Device $device_id pasif, addDiffData çalışmadı.");
            return 0;
        }

        if ($type == 'diff' && $targetData_id >= 200) {
            $type = 'sum';
            $data_id = $data_id + 100;
        }

        $triger = false;
        $first = false;
        $last = false;
        if (stristr($type, 'triger[')) {
            $time = str_replace(']', '', str_replace('triger[', '', $type));
            $type = 'triger';
            $start = Carbon::now()->subHours($time)->startOfDay()->addHours($time);
            $triger = Device::getValue($device_id, $data_id, $start, 60);
        } else {
            $rememberKey = sha1("first_" . $device_id . "_" . $data_id . "_" . $start);
            $first =  Cache::remember($rememberKey, 86400, function () use ($device_id, $data_id, $start) {
                return Device::getDayFirstValue($device_id, $data_id, $start);
            });

            $last = Device::getDayLastValue($device_id, $data_id, $end, $start);
            if ($default == -1) {
                $lastData = json_decode(Device::find($device_id)->last_data, true);
                $default = $lastData[$data_id] ?? 0;
            }
        }

        $rememberKey = sha1("data_" . $device_id . "_" . $targetData_id . "_" . $start);
        $data = Cache::remember($rememberKey, 86400, function () use ($device_id, $targetData_id, $start) {
            return DB::table('device_datas')
                ->where('device_id', $device_id)
                ->where('data_id', $targetData_id)
                ->where('created_at', $start)->first();
        });

        if ($first) {
            $firstValue = $first->value;
            if ($last) {
                if (
                    ($last->offset !== null && $first->offset !== null && $first->offset !== $last->offset) ||
                    ($last->multiplier !== null && $first->multiplier !== null && $first->multiplier !== $last->multiplier)
                ) {
                    // multiplier 0 kontrolü ekledik
                    if (!empty($first->multiplier) && $first->multiplier != 0) {
                        $firstValue = (($firstValue - $first->offset) / $first->multiplier) * $last->multiplier + $last->offset;
                    } else {
                        // multiplier 0 veya null ise fallback
                        Log::warning("Device {$device_id} - data_id {$data_id}: first->multiplier 0 olduğu için division atlandı.");
                        $firstValue = $firstValue; // istersen burada $default veya 0 da verebilirsin
                    }
                }
            }
        } else {
            $firstValue = $default;
        }

        if ($last) {
            $lastValue = $last->value;
        } else {
            $lastValue = $default;
        }

        switch ($type) {
            case 'last': $value = $lastValue; break;
            case 'first': $value = $firstValue; break;
            case 'max':
                $rememberKey = sha1("max_" . $device_id . "_" . $targetData_id . "_" . $start);
                $value = Cache::remember($rememberKey, 600, function () use ($device_id, $data_id, $start, $end) {
                    return DB::table('device_datas')
                        ->where('device_id', $device_id)
                        ->where('data_id', $data_id)
                        ->whereBetween('created_at', [$start, $end])
                        ->orderBy('created_at', 'desc')->max('value');
                });
                break;
            case 'min':
                $rememberKey = sha1("min_" . $device_id . "_" . $targetData_id . "_" . $start);
                $value = Cache::remember($rememberKey, 600, function () use ($device_id, $data_id, $start, $end) {
                    return DB::table('device_datas')
                        ->where('device_id', $device_id)
                        ->where('data_id', $data_id)
                        ->whereBetween('created_at', [$start, $end])
                        ->orderBy('created_at', 'desc')->min('value');
                });
                break;
            case 'avg':
                $rememberKey = sha1("avg_" . $device_id . "_" . $targetData_id . "_" . $start);
                $value = Cache::remember($rememberKey, 600, function () use ($device_id, $data_id, $start, $end) {
                    return DB::table('device_datas')
                        ->where('device_id', $device_id)
                        ->where('data_id', $data_id)
                        ->whereBetween('created_at', [$start, $end])
                        ->orderBy('created_at', 'desc')->avg('value');
                });
                break;
            case 'sum':
                $rememberKey = sha1("sum_" . $device_id . "_" . $targetData_id . "_" . $start);
                $value = Cache::remember($rememberKey, 600, function () use ($device_id, $data_id, $start, $end) {
                    return DB::table('device_datas')
                        ->where('device_id', $device_id)
                        ->where('data_id', $data_id)
                        ->whereBetween('created_at', [$start, $end])
                        ->orderBy('created_at', 'desc')->sum('value');
                });
                break;
            case 'triger': $value = $triger->value; break;
            default: $value = $lastValue - $firstValue; break;
        }

        $value = round($value, 2);

        if ($data) {
            DB::table('device_datas')->where('id', $data->id)->update(['value' => $value]);
        } else {
            DB::table('device_datas')->insert([
                "device_id" => $device_id,
                "data_id"   => $targetData_id,
                "value"     => $value,
                'created_at'=> $start,
                'hourly'    => $start,
                'multiplier'=> 1,
                'offset'    => 0
            ]);
        }
        return $value;
    }

    public static function getDayFirstValue($device_id, $data_id, $time)
    {
        $closedata = Device::getValue($device_id, $data_id, $time, 30, 30);
        if ($closedata) { // en yakın tarihli data varsa
            $data = $closedata;
        } else { // yoksa önceki günün son datası
            $islem = strtotime($time);
            $start = date('Y-m-d H:i:s', strtotime("-3 day", $islem));
            $data = DB::table('device_datas')
                ->where('device_id', $device_id)
                ->where('data_id', $data_id)
                ->whereBetween('created_at', [$start, $time])
                ->orderBy('created_at', 'desc')
                ->first();
        }
        //  if($data){
        //      dump('first','device',$device_id,'data',$data_id,$data->created_at);
        //  }else{
        //      dump('first','device',$device_id,'data',$data_id,'veri yok');
        // }
        return $data;
    }

    public static function getDayFirstValueOnCache($device_id, $data_id, $start)
    {
        $rememberKey = sha1("first_" . $device_id . "_" . $data_id . "_" . $start);
        $data =  Cache::remember($rememberKey, 86400, function () use ($device_id, $data_id, $start) {
            return Device::getDayFirstValue($device_id, $data_id, $start);
        });
        if ($data) {
            return $data->value;
        } else {
            return 0;
        }
    }

    public static function getDayLastValue($device_id, $data_id, $time, $start)
    {
        $islem = strtotime($time);
        if (Carbon::now()->timestamp < $islem) { // gün bitmediyse son veri
            $data = DB::table('device_datas')
                ->where('device_id', $device_id)
                ->where('data_id', $data_id)
                ->whereBetween('created_at', [$start, $time])
                ->orderBy('created_at', 'desc')
                ->first();
        } else { //gün bitmişse yakın veri
            $rememberKey = sha1("last_" . $device_id . "_" . $data_id . "_" . $start);
            if (Cache::has($rememberKey)) {
                $data =  Cache::get($rememberKey);
            } else {
                $data = Device::getValue($device_id, $data_id, $time, 30, 30);
                if ($data) {
                    $createdAt =  strtotime($data->created_at);
                    if ($createdAt > $islem || ($islem - $createdAt) + $islem <  Carbon::now()->timestamp)
                        $data =  Cache::remember($rememberKey, 86400, function () use ($data) {
                            return $data;
                        });
                } else {
                    $data = DB::table('device_datas')
                        ->where('device_id', $device_id)
                        ->where('data_id', $data_id)
                        ->whereBetween('created_at', [$start, $time])
                        ->orderBy('created_at', 'desc')
                        ->first();
                }
            }
        }
        /*  if($data){
            dump('last','device',$device_id,'data',$data_id,$data->created_at);
        }else{
            dump('last','device',$device_id,'data',$data_id,'veri yok');
        }*/
        return $data;
    }

    private static function getValue($device_id, $data_id, $time, $maxMinute = 720, $diffMinute = 60)
    {
        $islem = strtotime($time);
        $baslangic = strtotime("-$diffMinute minute", $islem);
        $bitis = strtotime("+$diffMinute minute", $islem);

        $data = DB::table('device_datas')
            ->where('device_id', $device_id)
            ->where('data_id', $data_id)
            ->whereBetween('created_at', [date('Y-m-d H:i:s', $baslangic), date('Y-m-d H:i:s', $bitis)])
            ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND,"' . date('Y-m-d H:i:s', $islem) . '" , created_at))')
            ->first();
        if ($data || $diffMinute >= $maxMinute) {
            return $data;
        } else {
            return Device::getValue($device_id, $data_id, $time, $maxMinute, $diffMinute + 60);
        }
    }

    public static function getdatas($device_id, $data_id, $hour, $order = 'asc')
    {

        if (!is_numeric($hour)) {
            $device = Device::find($device_id);
            $setting = CompanySetting::select('day_start_hour', 'week_start_day', 'month_start_day')->find($device->company_id);
            $start = Carbon::now()->subHours($setting['day_start_hour'])->startOfDay()->addHours($setting['day_start_hour']);
        }

        switch ($hour) {
            case 'D':
                $start = $start->format('Y-m-d H:i:s');
                $hour = 24;
                break;
            case 'W':
                $start->startOfWeek($setting['week_start_day'])->addHours($setting['day_start_hour'])->format('Y-m-d H:i:s');
                $hour = 168;
                break;
            case 'M':
                $start->startOfMonth()->addDays($setting['month_start_day'] - 1)->addHours($setting['day_start_hour'])->format('Y-m-d H:i:s');
                $hour = 720;
                break;
            case 'Y':
                $start->startOfYear()->addDays($setting['month_start_day'] - 1)->addHours($setting['day_start_hour'])->format('Y-m-d H:i:s');
                $hour = 8640;
                break;
            default:
                $start = date('Y-m-d H:i:s', strtotime("- $hour hour"));
                break;
        }

        $values = DB::table('device_datas')
            ->where('device_id', $device_id)
            ->where('data_id', $data_id)
            ->whereBetween('created_at', [$start, date('Y-m-d H:i:s')])
            ->groupBy(DB::raw("FLOOR(UNIX_TIMESTAMP(created_at)/($hour / 6))"))->orderBy('created_at', $order);

        //  return $values->get(DB::raw(' if(max(value) - avg(value) > avg(value) - min(value),min(value),max(value) ) value, min(`created_at`) created_at'));
        return $values->get(DB::raw(' max(value) value, min(`created_at`) created_at'));
    }

    public function resetSub($time)
    {
        $subDevices = $this->findSubs();

        foreach ($subDevices as $subDevice) {
            $subDevice->resetSub($time);
            $subDevice->reCalculate($time);
        }
    }

    /*
    return Device 
    */

    protected function findSubs()
    {
        return  Device::where('formula', 'like', "%[" . $this->id . "_%")->get();
    }

   protected function reCalculate($time)
    {
        // pasif cihazı atla
        if ($this->status == 0) {
            Log::info("Device {$this->id} pasif, reCalculate yapılmadı.");
            return;
        }

        $setting = CompanySetting::select('day_start_hour', 'week_start_day', 'month_start_day')->find($this->company_id);
        $baseStart = Carbon::parse($time)->subHours($setting['day_start_hour'])->startOfDay()->addHours($setting['day_start_hour']);
        $tags = json_decode($this->formula);
        $allTags = json_decode($this->tags, true);
        $types = json_decode($this->type, true);
        $degistir = array();

        foreach ($tags as $tag) {
            $match = array();
            preg_match_all('/\[(.*?)\]/', $tag, $match);
            if (is_array($match[1])) {
                foreach ($match[1] as $device_dataid) {
                    $device = explode("_", $device_dataid);
                    $value = self::getValue($device[0], $device[1], $baseStart);
                    $degistir[$device_dataid] = $value ? $value->value : 0;
                }
            }
        }

        foreach ($tags as $data_id => $tag) {
            foreach ($degistir as $key => $value) {
                $tag = str_replace($key, $value, $tag);
            }

            $result = Device::calculate($tag);
            DeviceData::where('device_id', $this->id)->where('data_id', $data_id)->where('created_at', $baseStart)->delete();
            DB::table('device_datas')->insert(['device_id' => $this->id, 'data_id' => $data_id, 'value' => $result, 'created_at' => $baseStart, 'hourly' => $baseStart]);

            $start = clone $baseStart;
            $type = 'diff';
            if (isset($allTags[$data_id + 200])) {
                $end = clone $start;
                $end->addHours(24);
                if (isset($types[$data_id])) {
                    $type = $types[$data_id];
                }
                Device::addDiffData($this->id, $data_id, $data_id + 200, $start->toDateTimeString(), $end->toDateTimeString(), $type);
            }
            if (isset($allTags[$data_id + 300])) {
                $start->startOfWeek($setting['week_start_day'])->addHours($setting['day_start_hour']);
                $end = clone $start;
                $end = $end->addDays(7);
                if (isset($types[$data_id])) {
                    $type = $types[$data_id];
                }
                Device::addDiffData($this->id, $data_id, $data_id + 300, $start->toDateTimeString(), $end->toDateTimeString(), $type);
            }
            if (isset($allTags[$data_id + 400])) {
                $start->startOfMonth()->addDays($setting['month_start_day'] - 1)->addHours($setting['day_start_hour']);
                $end = clone $start;
                $end = $end->endOfMonth()->addDays($setting['month_start_day'] - 1)->addHours($setting['day_start_hour']);
                if (isset($types[$data_id])) {
                    $type = $types[$data_id];
                }
                Device::addDiffData($this->id, $data_id, $data_id + 400, $start->toDateTimeString(), $end->toDateTimeString(), $type);
            }
            if (isset($allTags[$data_id + 500])) {
                $start->startOfYear()->addDays($setting['month_start_day'] - 1)->addHours($setting['day_start_hour']);
                $end = clone $start;
                $end = $end->endOfYear()->addDays($setting['month_start_day'] - 1)->addHours($setting['day_start_hour']);
                if (isset($types[$data_id])) {
                    $type = $types[$data_id];
                }
                Device::addDiffData($this->id, $data_id, $data_id + 500, $start->toDateTimeString(), $end->toDateTimeString(), $type);
            }
        }
    }

    private static function addhourly($device_id, $data_id, $last_hour)
    {
        $sonsaat = strtotime($last_hour);
        $islem = strtotime("+60 minute", $sonsaat);
        $baslangic = strtotime("+30 minute", $sonsaat);
        $bitis = strtotime("+90 minute", $sonsaat);
        $yakintarih = DB::table('device_datas')
            ->where('device_id', $device_id)
            ->where('data_id', $data_id)
            ->whereBetween('created_at', [date('Y-m-d H:i:s', $baslangic), date('Y-m-d H:i:s', $bitis)])
            ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND,"' . date('Y-m-d H:i:s', $islem) . '" , created_at))')
            ->first();
        if (isset($yakintarih->created_at)) {
            $arti_islem = 2 * $islem - strtotime($yakintarih->created_at);
            if (strtotime($yakintarih->created_at) > $islem || strtotime("now") > $arti_islem) {
                DB::table('device_datas')->where('id', $yakintarih->id)->update(['hourly' => date('Y-m-d H:i:s', $islem)]);
            }
            return true;
        } else {
            return false;
        }
    }

}
