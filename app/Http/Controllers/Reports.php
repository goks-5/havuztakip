<?php

namespace App\Http\Controllers;

use App\CompanySetting;
use App\DataType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataUpdated;

use App\Device;
use App\Report;
use App\Http\Controllers\VoyagerBaseController;

use TCG\Voyager\Facades\Voyager;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

use App\Exports\ReportExport;

class Reports extends VoyagerBaseController
{
    public function edit(Request $request, $id)
    {
        $view = 'voyager::raporlar.edit-add';
        $data['report'] =  Report::where('company_id', Auth::user()->company_id)->where('id', $id)->first();
        $data['devices'] =   Device::where('company_id', Auth::user()->company_id)->get();
        return Voyager::view($view, $data);
    }


    public function excel(Request $request, $id)
    {
        $view = 'voyager::raporlar.edit-add';
        $report =  Report::where('company_id', Auth::user()->company_id)->where('id', $id)->first();
        $lenght = $report->lenght;
        $type = $report->type;
        $tags = json_decode($report->tags, true);
        $titles = json_decode($report->titles, true);
        if (isset($request->date)) {
            $date = date('Y-m-d H:i', strtotime($request->date));
        } else {
            $date = date('Y-m-d');
        }

        $setting = CompanySetting::select('day_start_hour', 'week_start_day', 'month_start_day')->find(Auth::user()->company_id);
        switch ($report->period) {
            case 1:
                $dateStart = Carbon::parse(strtotime($date . " -$report->lenght day"))
                    ->startOfDay()->addHours($setting['day_start_hour'])->toDateTimeString();
                $dataDiff = 200;
                $dateparam = "day";
                break;
            case 2:
                $dateStart = Carbon::parse(strtotime($date . " -$report->lenght week"))
                    ->startOfDay()->startOfWeek($setting['week_start_day'])->addHours($setting['day_start_hour'])->toDateTimeString();
                $dataDiff = 300;
                $dateparam = "week";
                break;
            default:
                $dateStart = Carbon::parse(strtotime($date . " -$report->lenght month"))
                    ->startOfDay()->startOfMonth()->addDays($setting['month_start_day'] - 1)->addHours($setting['day_start_hour'])->toDateTimeString();
                $dataDiff = 400;
                $dateparam = "month";
                break;
        }


        $gunler = array(
            'Pazartesi',
            'Salı',
            'Çarşamba',
            'Perşembe',
            'Cuma',
            'Cumartesi',
            'Pazar'
        );

        $aylar = array(
            'Ocak',
            'Şubat',
            'Mart',
            'Nisan',
            'Mayıs',
            'Haziran',
            'Temmuz',
            'Ağustos',
            'Eylül',
            'Ekim',
            'Kasım',
            'Aralık'
        );



        if ($type == 1 || $type == 3) {
            $index = 0;
            foreach ($tags as $key => $tag) {
                $device = explode('_', $tag);

                $data[$index]['Sayaç'] = rtrim(rtrim(rtrim($titles[$key], 'Günlük'), 'Haftalık'), 'Aylık') . 'Endeks';
                $data[$index + 1]['Sayaç'] = $titles[$key];
                $veriler = DB::table('device_datas')
                    ->select('created_at', 'value')
                    ->where('device_id', $device[0])
                    ->where('data_id', $device[1])
                    ->where('created_at', '<', $date)
                    ->where('created_at', '>=', $dateStart)
                    ->limit($lenght)->orderBy('created_at', $report->order_direction)->get();
                $veriArray = [];
                foreach ($veriler as $veri) {
                    $ay = $aylar[date('m', strtotime($veri->created_at)) - 1];
                    $gun = $gunler[date('N', strtotime($veri->created_at)) - 1];
                    $veriArray[date('d', strtotime($veri->created_at)) . " " . $ay] = $veri->value;
                }
                if ($report->order_direction == 'desc') {
                    for ($addDate = $lenght; $addDate >= 0; $addDate--) {
                        $onDate = date('Y-m-d H:i', strtotime($dateStart . " +$addDate $dateparam"));
                        $ay = $aylar[date('m', strtotime($onDate)) - 1];
                        $gun = $gunler[date('N', strtotime($onDate)) - 1];
                        if ($type == 3) {
                            $data[$index][date('d', strtotime($onDate)) . " " . $ay] = Device::getDayFirstValueOnCache($device[0],  $device[1] - $dataDiff, $onDate);
                        }
                        $data[$index + 1][date('d', strtotime($onDate)) . " " . $ay] =  $veriArray[date('d', strtotime($onDate)) . " " . $ay] ?? '-';
                    }
                } else {
                    for ($addDate = 0; $addDate <= $lenght; $addDate++) {
                        $onDate = date('Y-m-d H:i', strtotime($dateStart . " +$addDate $dateparam"));
                        $ay = $aylar[date('m', strtotime($onDate)) - 1];
                        $gun = $gunler[date('N', strtotime($onDate)) - 1];
                        if ($type == 3) {
                            $data[$index][date('d', strtotime($onDate)) . " " . $ay] = Device::getDayFirstValueOnCache($device[0],  $device[1] - $dataDiff, $onDate);
                        }
                        $data[$index + 1][date('d', strtotime($onDate)) . " " . $ay] =  $veriArray[date('d', strtotime($onDate)) . " " . $ay] ?? '-';
                    }
                }

                ++$index;
                ++$index;
            }
            return Excel::download(new ReportExport($data), $report->name . '.xlsx');
        } else {


            foreach ($tags as $key => $tag) {
                $device = explode('_', $tag);

                $veriler = DB::table('device_datas')
                    ->select('created_at', 'value')
                    ->where('device_id', $device[0])
                    ->where('data_id', $device[1])
                    ->where('created_at', '<', $date)
                    ->where('created_at', '>=', $dateStart)
                    ->limit($lenght)->orderBy('created_at', $report->order_direction)->get();
                $veriArray = [];
                foreach ($veriler as $veri) {
                    $ay = $aylar[date('m', strtotime($veri->created_at)) - 1];
                    $gun = $gunler[date('N', strtotime($veri->created_at)) - 1];
                    $tarih = date('d', strtotime($veri->created_at)) . " " . $ay . " " . date('Y', strtotime($veri->created_at)) . " " . $gun;
                    $veriArray[$tarih] = $veri->value;
                }
                if ($report->order_direction == 'desc') {
                    for ($addDate = $lenght; $addDate >= 0; $addDate--) {
                        $onDate = date('Y-m-d H:i', strtotime($dateStart . " +$addDate $dateparam"));
                        $ay = $aylar[date('m', strtotime($onDate)) - 1];
                        $gun = $gunler[date('N', strtotime($onDate)) - 1];
                        $tarih = date('d', strtotime($onDate)) . " " . $ay . " " . date('Y', strtotime($onDate)) . " " . $gun;
                        $data[$tarih]['Tarih']  = $tarih;
                        if ($type == 4) {
                            $data[$tarih][rtrim(rtrim(rtrim($titles[$key], 'Günlük'), 'Haftalık'), 'Aylık') . 'Endeks'] = Device::getDayFirstValueOnCache($device[0],  $device[1] - $dataDiff, $onDate);
                        }
                        $data[$tarih][$titles[$key]] =  $veriArray[$tarih] ?? '-';
                    }
                } else {
                    for ($addDate = 0; $addDate <= $lenght; $addDate++) {
                        $onDate = date('Y-m-d H:i', strtotime($dateStart . " +$addDate $dateparam"));
                        $ay = $aylar[date('m', strtotime($onDate)) - 1];
                        $gun = $gunler[date('N', strtotime($onDate)) - 1];
                        $tarih = date('d', strtotime($onDate)) . " " . $ay . " " . date('Y', strtotime($onDate)) . " " . $gun;
                        $data[$tarih]['Tarih']  = $tarih;
                        if ($type == 4) {
                            $data[$tarih][rtrim(rtrim(rtrim($titles[$key], 'Günlük'), 'Haftalık'), 'Aylık') . 'Endeks'] = Device::getDayFirstValueOnCache($device[0],  $device[1] - $dataDiff, $onDate);
                        }
                        $data[$tarih][$titles[$key]] =  $veriArray[$tarih] ?? '-';
                    }
                }
            }
            return Excel::download(new ReportExport(array_values($data)), $report->name . '.xlsx');
        }
    }
    public function create(Request $request)
    {
        $view = 'voyager::raporlar.edit-add';

        $data['devices'] =  $devices = Device::where('company_id', Auth::user()->company_id)->get();
        return Voyager::view($view, $data);
    }


    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = DataType::where('slug', '=', $slug)->first();
        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();

        $request->merge(['company_id' => Auth::user()->company_id]);

        //dd($request);
        $request->merge(['tags' => json_encode($request->tags)]);
        $request->merge(['titles' => json_encode($request->titles)]);
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());


        event(new BreadDataAdded($dataType, $data));

        if (!$request->has('_tagging')) {
            if (auth()->user()->can('browse', $data)) {
                $redirect = redirect()->route("voyager.{$dataType->slug}.index");
            } else {
                $redirect = redirect()->back();
            }

            return $redirect->with([
                'message'    => __('voyager::generic.successfully_added_new') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
    }
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);


        $dataType = DataType::where('slug', '=', $slug)->first();
        // Compatibility with Model binding.
        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
            $model = $model->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses($model))) {
            $data = $model->withTrashed()->findOrFail($id);
        } else {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);
        }

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();

        $dataAll = $request->all();
        if (!isset($dataAll['company_id']) || $dataAll['company_id'] ==  Auth::user()->company_id) {
            $request->merge(['company_id' => Auth::user()->company_id]);
            $this->insertUpdateData($request, $slug, $dataType->editRows, $data);
        }

        event(new BreadDataUpdated($dataType, $data));

        if (auth()->user()->can('browse', $model)) {
            $redirect = redirect()->route("voyager.{$dataType->slug}.index");
        } else {
            $redirect = redirect()->back();
        }

        return $redirect->with([
            'message'    => __('voyager::generic.successfully_updated') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }
}
