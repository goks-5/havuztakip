<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Device;
use App\Fault;
use App\ProductionTag;
use Illuminate\Support\Facades\Cache;

class DashboardTool extends Model
{
    public function ajaxdata($tools)
    {
        $return = array();
        foreach ($tools as $tool) {
            $type = $tool->type;
            $settings = json_decode($tool->settings, true);
            $return[$type]['tool_' . $tool->id] = $this->$type($settings, $tool->id);
        }
        return json_encode($return);
    }

    public function device_data_gauge($settings, $tool)
    {
        $devices = Device::where('id', $settings['device'])->first();
        $lastdata = json_decode($devices->last_data, true);
        return ['value' => $lastdata[$settings['device_index']], 'last_at' => $devices->last_at, 'tool' => $tool];
    }

    public function device_meter($settings, $tool)
    {
        $devices = Device::where('id', $settings['device'])->first();
        $lastdata = json_decode($devices->last_data, true);
        return ['value' => $lastdata[$settings['device_index']], 'last_at' => $devices->last_at, 'tool' => $tool];
    }
    public function device_indicator($settings, $tool)
    {
        $devices = Device::where('id', $settings['device'])->first();
        $lastdata = json_decode($devices->last_data, true);
        $data = (float)$lastdata[$settings['device_index']];
        $color = '#000000';
        foreach ($settings['indicator'] as  $value) {
            if ($data >= (float)$value['start']) {
                $color = $value['color'];
            }
        }
        return ['value' => $lastdata[$settings['device_index']], 'last_at' => $devices->last_at, 'tool' => $tool, 'color' => $color, 'size' => $settings['size']];
    }

    public function device_alarm($settings, $tool)
    {
        $devices = Device::where('id', $settings['device'])->first();
        $lastdata = json_decode($devices->last_data, true);
        $data = (float)$lastdata[$settings['device_index']];
        $alarm = false;
        if ($data >= (float)$settings['start'] && $settings['opt'] == '>=') {
            $alarm = true;
        }
        if ($data <= (float)$settings['start'] && $settings['opt'] == '<=') {
            $alarm = true;
        }

        return ['value' => $lastdata[$settings['device_index']], 'tool' => $tool, 'alarm' => $alarm, 'sound' => $settings['sound']];
    }
    public function device_data($settings, $tool)
    {
        $devices = Device::where('id', $settings['device'])->first();
        if ($devices) {
            $lastdata = json_decode($devices->last_data, true);
            return $lastdata[$settings['device_index']] ?? "-";
        } else {
            return "-";
        }
    }

    public function last_date($settings, $tool)
    {
        $devices = Device::where('id', $settings['device'])->first();
        if ($devices) {
            return $devices->last_at;
        } else {
            return "-";
        }
    }

    public function switch($settings, $tool)
    {
        $devices = Device::where('id', $settings['device'])->first();
        if ($devices) {
            $lastdata = json_decode($devices->last_data, true);
            return $lastdata[$settings['device_index']] ?? "-";
        } else {
            return "-";
        }
    }

    public function device_daily_data($settings, $tool)
    {
        if ($settings['start_hour'] > date('H')) {
            $settings['day'] = $settings['day'] + 1;
        }
        $farksaat = (($settings['day']) * 24)  - $settings['start_hour'] + date('H');
        $farksaat2 = (($settings['day'] - 1) * 24)  - $settings['start_hour'] + date('H');
        $start = date("Y-m-d H:00:00", strtotime("-{$farksaat} hour"));
        $end = date("Y-m-d H:00:00", strtotime("-{$farksaat2} hour"));
        if (strtotime($end) > strtotime('now')) {
            $end = date("Y-m-d H:i:s");
            return  DB::select(" SELECT device_date_sub({$settings['device']},{$settings['device_index']},'{$start}','{$end}') as sub_value")[0]->sub_value;
        } else {
            return  DB::select(" SELECT device_date_sub_hourly({$settings['device']},{$settings['device_index']},'{$start}','{$end}') as sub_value")[0]->sub_value;
        }
    }

    public function period($settings, $tool)
    {
        if ($settings['data_type'] ?? 0) {
            $value['cols'][] = ['id' => 0, 'label' => 'Tarih', 'type' => 'string'];
        } else {
            $value['cols'][] = ['id' => 0, 'label' => 'Cihaz', 'type' => 'string'];
        }

        if ($settings['order_asc'] ?? 0) {         
            $order = "asc";
        } else {
            $order = "desc";
        }

        $devices = array();
        $timearray = array();
        $colindex = 1;
        setlocale(LC_TIME, 'tr_TR.utf8');


        foreach ($settings['devices'] as $key => $device) {
            if (!isset($devices[$device['device']])) {
                $devices[$device['device']] = Device::where('id', $device['device'])->first();
            }
            $cdevice =   $devices[$device['device']];
            $tags = json_decode($cdevice->tags, true);

            $rows = Device::getdatas($device['device'], $device['device_index'], $settings['hour'], $order);
            if ($settings['data_type'] ?? 0) {
                $value['cols'][] = ['id' => $colindex, 'label' =>  $tags[$device['device_index']], 'type' => 'number'];
                ++$colindex;
                foreach ($rows as $row) {
                    $time = Carbon::createFromTimestamp(strtotime($row->created_at));
                    $time = $time->formatLocalized('%a %d %b %Y');
                    $timeindex = array_search($time, $timearray);
                    if (!in_array($time, $timearray)) {
                        $timearray[] = $time;
                    }           
                }
                foreach ($rows as $row) {
                    $time = Carbon::createFromTimestamp(strtotime($row->created_at));
                    $time = $time->formatLocalized('%a %d %b %Y');
                    $timeindex = array_search($time, $timearray);
                    $value['rows'][$timeindex]['c'][0]['v'] = $time;
                    $value['rows'][$timeindex]['c'][$key + 1]['v'] = $row->value;
                }
            } else {
                $value['rows'][$key]['c'][0]['v'] = $tags[$device['device_index']];
                foreach ($rows as $row) {
                    $time = Carbon::createFromTimestamp(strtotime($row->created_at));
                    $time = $time->formatLocalized('%a %d %b %Y');
                    if (!in_array($time, $timearray)) {
                        $timearray[] = $time;
                        $value['cols'][] = ['id' => $colindex, 'label' => $time, 'type' => 'number'];
                        ++$colindex;
                    }
                    $timeindex = array_search($time, $timearray);
                    $value['rows'][$key]['c'][$timeindex + 1]['v'] = $row->value;
                }
            }
        }

        return $value;
    }
    
    public function sum_tag($settings, $tool)
    {
        $tags = [];
        $devices = [];
        $total['label'] = $settings['title'];
        $total['value'] = 0;
        $unit = $settings['unit'] ?? "";
        foreach ($settings['devices'] as $key => $device) {
            if (!isset($devices[$device['device']])) {
                $devices[$device['device']] = Device::where('id', $device['device'])->first();
            }
            if ( $devices[$device['device']]) {
                $currrentDevice =  $devices[$device['device']];
                $deviceTags = json_decode($currrentDevice->tags, true);
                $lastdata = json_decode($currrentDevice->last_data, true);
                $label = $deviceTags[$device['device_index']] ??  "-";
                $value = $lastdata[$device['device_index']] ?? 0;
                $total['value'] += $value;
                $tags[] = ['label' => $label, 'value' => $value   . " $unit" ];
            } 
        }
        
        $total['value'] .=  " $unit";
        return ['total' => $total , 'tags' => $tags];
    }

    public function go_to_tab_button($settings, $tool)
    {
        // Dashboard verilerini tutmak için boş bir array oluşturuyoruz.
        $dashboards = [];
        
        // Kullanıcının kaydettiği dashboard ID'sini alıyoruz.
        $dashboardId = $settings['dashboard_id'] ?? null;
        
        // Eğer dashboard ID varsa, o dashboard'u veritabanından buluyoruz.
        if ($dashboardId) {
            $dashboard = Dashboard::where('id', $dashboardId)->first();
            
            if ($dashboard) {
                // Dashboard bilgilerini alıyoruz (başlık ve ID)
                $label = $dashboard->title;
                $id = $dashboard->id;
                
                // Tags mantığıyla yönlendirme butonuna tıklanacak dashboard verisini ekliyoruz.
                $dashboards[] = ['label' => $label, 'id' => $id];
            }
        }
    
        // Eğer bir dashboard ID bulunmazsa, kullanıcıya "Dashboard seçilmemiş" mesajını gösterecek veriyi hazırlıyoruz.
        $total['label'] = "Dashboard";
        $total['value'] = $dashboardId ? $dashboard->title : "Dashboard seçilmemiş";
    
        // Kullanıcıyı ilgili dashboard'a yönlendirecek buton bilgilerini döndürüyoruz.
        return [
            'total' => $total,
            'dashboards' => $dashboards
        ];
    }    

    public function device_chart($settings, $tool)
    {
        $value['cols'][] = ['id' => 0, 'label' => 'Tarih', 'type' => 'datetime'];
        $devices = array();
        foreach ($settings['devices'] as $key => $device) {
            if (!isset($devices[$device['device']])) {
                $devices[$device['device']] = Device::where('id', $device['device'])->first();
            }
            $cdevice =   $devices[$device['device']];
            $tags = json_decode($cdevice->tags, true);

            $value['cols'][] = ['id' => $key + 1, 'label' => $tags[$device['device_index']], 'type' => 'number'];
            $rows = Device::getdatas($device['device'], $device['device_index'], $settings['hour']);
            $c = array();
            foreach ($rows as $row) {
                $time = strtotime($row->created_at);
                $c['c'][0]['v'] = "Date(" . date('Y', $time) . "," . ((int)date('n', $time) - 1)  . "," . date('j', $time) . "," . date('G', $time) . "," . date('i', $time) . "," . date('s', $time) . ")";
                $c['c'][$key + 1]['v']  = $row->value;
                $value['rows'][] = $c;
            }
        }

        return $value;
    }

    public function faults($settings, $tool)
    {
        $value['fault'][] = ['Durum', 'Adet'];
        $value['fault'][] = ['Yeni Arıza', Fault::where('company_id', Auth::user()->company_id)->where('status', 'Yeni')->count()];
        $value['fault'][] = ['Onay Bekleyen', Fault::where('company_id', Auth::user()->company_id)->where('status', 'Onay |1|')
            ->where('created_at', '>=', Carbon::now()->subHours('24')->toDateTimeString())->count()];
        $value['fault'][] = ['Beklemede Olan', Fault::where('company_id', Auth::user()->company_id)->where('status', 'Bekliyor |0|')->count()];
        $value['fault'][] = ['Bakıma Başlanan', Fault::where('company_id', Auth::user()->company_id)->where('status', 'Bakıma Başlandı |0|')->count()];
        $value['fault'][] = ['Parça Bekleyen', Fault::where('company_id', Auth::user()->company_id)->where('status', 'Malzeme Bekliyor |2|')->count()];
        $value['fault'][] = ['Firmaya Yönlendirilen', Fault::where('company_id', Auth::user()->company_id)->where('status', 'Firma Yönlendirildi |2|')->count()];

        return $value;
    }

    public function faults_table($settings, $tool)
    {
        $faults = Fault::where('company_id', Auth::user()->company_id)
            ->whereIn('status', $settings['status'])
            ->orderBy('created_at', 'desc')
            ->limit($settings['limit'])->get();
        $return = [];
        foreach ($faults as $fault) {
            $return[] = [
                'id' => $fault->id,
                'equipment' => $fault->equipment->name,
                'staff' => $fault->staff->name ?? null,
                'created_at' => $fault->created_at->format('Y-m-d H:i:s'),
                'accepted_at' =>  $fault->accepted_at ? $fault->accepted_at->format('Y-m-d H:i:s') : null,
                'fault_code' => $fault->fault_code,
                'fault_comment' => $fault->fault_comment,
                'reporting_user' => $fault->reporting_user,
                'status' => $fault->status,

            ];
        }

        return $return;
    }

    public function backgroud($settings, $tool)
    {

        return null;
    }
    public function text($settings, $tool)
    {

        return null;
    }

    public function saveDeviceData(Request $request)
    {
    $device = $request->input('device');
    $label = $request->input('label');

    // Burada veritabanı işlemlerini gerçekleştirin
    $deviceData = new DeviceData();
    $deviceData->device = $device;
    $deviceData->label = $label;
    $deviceData->save();

    return response()->json(['status' => 'success', 'message' => 'Veri başarıyla kaydedildi']);
    }

    public function addDashboardVisual(Request $request)
    {   
    $device = $request->input('device');
    $label = $request->input('label');

    // Görsel ekleme işlemleri
    $data = DeviceData::where('device', $device)->where('label', $label)->first();

    // Görsel ekleme kodları buraya eklenebilir
    // Örnek: Blade template ile veri gösterme
    return view('dashboard.visual', ['data' => $data]);
    }

    public function tags($settings, $tool)
    {
        /*
        $companysobj = DB::table('company_users')->where('user_id', Auth::user()->id)->get('company_id');
        $companys = array();
        foreach ($companysobj as $value) {
            $companys[] = $value->company_id;
        }*/
        $tags =   ProductionTag::where('company_id', Auth::user()->company_id)->where('end_time', null)->orderBy('id')->get();
        $result = array();
        foreach ($tags as $key => $tag) {
            $result[$tag->id]['id'] =  $tag->id;
            $result[$tag->id]['name'] =  $tag->name;
            $result[$tag->id]['start_time'] =  $tag->start_time;
            $result[$tag->id]['end_time'] =  $tag->end_time;
            $result[$tag->id]['tags_group'] =  $tag->tags_group;
            $data_tags = json_decode($tag->data_tags, true);

            foreach ($data_tags as $dt) {
                if (!isset($device[$dt['device']])) {
                    $tempDevice =  Device::select(
                        "name",
                        "tags"
                    )->withTrashed() 
                        ->where('id', $dt['device'])
                        ->first();
                    if( $tempDevice){
                        $device[$dt['device']] = $tempDevice;
                    }else{
                    continue;
                    }                       
                }
              

                $devicetags = json_decode($device[$dt['device']]->tags, true);

                $st = date('Y-m-d H:i:s', strtotime($tag->start_time));
                if ($tag->end_time > $tag->start_time) {
                    $et = date('Y-m-d H:i:s', strtotime($tag->end_time));;
                } else {
                    $et = date('Y-m-d H:i:s');
                }

                $result[$tag->id]['tags'][] = [
                    'name' => $device[$dt['device']]->name . ' - ' . $devicetags[$dt['device_index']],
                    'value' => Cache::remember('pt_' . $tag->id . '_' . $device[$dt['device']]->name . '_' . $devicetags[$dt['device_index']], 300, function () use ($dt, $st, $et) {
                        return  DB::select(" SELECT device_date_sub({$dt['device']},{$dt['device_index']},'{$st}','{$et}') as sub_value")[0]->sub_value;
                    })
                ];
            }
        }


        return $result;
    }
}
