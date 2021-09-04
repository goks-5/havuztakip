<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Device;
use App\DeviceData;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DeviceDatasExport;
use App\TagAccess;
use App\Company;
use App\CompanySetting;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Facades\Voyager;

class Devices extends VoyagerBaseController
{
    private $companymodels = ["App\Report", "App\Equipment", "App\Field", "App\Maintenance", "App\Device", "App\DashboardRow", "App\Staff", "App\Fault", "App\ProductionTag", "App\CompanySetting", "App\GroupTag", "App\TagAccess", "App\ReplacementPart", "App\StockMovement"];
    private $dashboardmodels = ["App\DashboardRow"];

    public function DevicesDatas()
    {
        $this->authorize('data', app('App\Device'));
        $data['devices'] = Device::where('company_id', Auth::user()->company_id)->get();
        $data['culumns'] = ['Cihaz ID', 'Takma Ad', 'Son Veri Tarihi', 'Son Veri'];
        return view('voyager::cihazlar.veri', $data);
    }

    public function DeviceDatas($id)
    {
        $this->authorize('data', app('App\Device'));
        $devices = Device::where('company_id', Auth::user()->company_id)->where('id', $id)->first();
        $tags = json_decode($devices->tags, true);
        $data['columns'] = $tags;
        $data['device_id'] = $devices->id;
        $data['mac'] = $devices->mac;
        return view('voyager::cihazlar.veriler', $data);
    }

    public function addManuelData(Request $request)
    {
        $device = Device::where('company_id', Auth::user()->company_id)->where('id', $request->id)->first();
        if ($device) {
            $time = date('Y-m-d H:i', strtotime($request->date));
            foreach ($request->tags as $key => $tag) {
                $deviceData = new DeviceData;
                $deviceData->device_id = $request->id;
                $deviceData->data_id = $key;
                $deviceData->value = $tag;
                $deviceData->created_at = $time;
                $deviceData->save();
            }
        }
        $device->last_at = $time;
        $lastdata = array();
        if (!empty($device->last_data)) {
            $lastdata = json_decode($device->last_data, true);
        }
        $replace = array_replace($lastdata, $request->tags);
        ksort($replace);
        $device->last_data = json_encode($replace);
        $device->save();
        // $device->resetSub($time);   
        // die();  
        return back();
    }

    public function DeviceDatasSearch(Request $request)
    {
        $this->authorize('data', app('App\Device'));
        $devices = Device::where('company_id', Auth::user()->company_id)->where('id', $request->id)->first();
        $tags = json_decode($devices->tags, true);
        $tagskeys = array();
        foreach ($tags as $key => $value) {
            $tagskeys[] = $key;
        }
        $countCacheName = "device_{$devices->id}";
        $count = DeviceData::select(DB::raw('count(DISTINCT created_at) adet'))
            ->where('device_id', $devices->id);
        $datas = DeviceData::select('created_at', DB::raw("concat('{',GROUP_CONCAT('\"',data_id,'\":',`value`),'}')  as jdata "))
            ->whereIn('data_id', $tagskeys)
            ->where('device_id', $devices->id);
        if (strtotime($request->endtime) > strtotime($request->starttime)) {
            $countCacheName .= strtotime($request->starttime) . "-" . strtotime($request->endtime);
            $count = $count->whereBetween('created_at', [$request->starttime, $request->endtime]);
            $datas = $datas->whereBetween('created_at', [$request->starttime, $request->endtime]);
        }
        $settings = CompanySetting::where('company_id', Auth::user()->company_id)->first();
        if ($settings) {
            $starthour = $settings->day_start_hour;
        } else {
            $starthour = 7;
        }
        if (isset($request->period) && $request->period <> "all") {
            switch ($request->period) {
                case 'hour':
                    $count = $count->whereNotNull('hourly');
                    $datas = $datas->whereNotNull('hourly');
                    break;
                case 'day':
                    $count = $count->where(DB::raw('Hour(hourly)'), $starthour);
                    $datas = $datas->where(DB::raw('Hour(hourly)'), $starthour);
                    break;
                case 'week':
                    $count = $count->where(DB::raw('Hour(hourly)'), $starthour);
                    $datas = $datas->where(DB::raw('Hour(hourly)'), $starthour);
                    $count = $count->where(DB::raw('DAYOFWEEK(hourly)'), 2);
                    $datas = $datas->where(DB::raw('DAYOFWEEK(hourly)'), 2);
                    break;
                case 'month':
                    $count = $count->where(DB::raw('Hour(hourly)'), $starthour);
                    $datas = $datas->where(DB::raw('Hour(hourly)'), $starthour);
                    $count = $count->where(DB::raw('DAY(hourly)'), 1);
                    $datas = $datas->where(DB::raw('DAY(hourly)'), 1);
                    break;
            }
        }

        if ($request->excel) {
            //    $datas  =   $datas->groupBy(DB::raw(' UNIX_TIMESTAMP(`created_at`) DIV 60'))->orderBy('created_at', 'desc')->get();
            $datas  =   $datas->groupBy(DB::raw(' UNIX_TIMESTAMP(`created_at`)'))->orderBy('created_at', 'desc')->get();
        } else {
            //  $datas  =   $datas->groupBy(DB::raw(' UNIX_TIMESTAMP(`created_at`) DIV 60'))->orderBy('created_at', $request->order[0]['dir'])->offset($request->start)->limit($request->length)->get();
            $datas  =   $datas->groupBy(DB::raw(' UNIX_TIMESTAMP(`created_at`)'))->orderBy('created_at', $request->order[0]['dir'])->offset($request->start)->limit($request->length)->get();
        }
        $regulardata = array();
        foreach ($datas as $data) {
            $writedata = array();
            if ($request->excel) {
                $writedata['Tarih'] = $data->created_at;
            } else {
                $writedata['Tarih'] = Carbon::parse($data->created_at)->locale('tr_TR')->isoFormat('D MMMM YYYY ddd H:mm::ss');
            }
            $dataarray = json_decode($data->jdata, true);
            foreach ($tags as $key => $tag) {
                if (isset($dataarray[$key])) {
                    $writedata[$tag] = (float)$dataarray[$key];
                    $ilk['Tarih'] = $writedata['Tarih'];
                    $ilk[$tag] = (float)$dataarray[$key];
                    if (!isset($son['Tarih']) ||  $son['Tarih'] < $writedata['Tarih']) {
                        $son['Tarih'] = $writedata['Tarih'];
                    }
                    if (!isset($son[$tag])) {
                        $son[$tag] = (float)$dataarray[$key];
                    }
                } else {
                    $writedata[$tag] = "";
                }
            }
            $regulardata[] = $writedata;
        }

        if ($request->excel) {
            if (isset($request->dif_data)) {
                //  $ilk=$regulardata[count($regulardata)-1];
                //  $son=$regulardata[0];

                $dif_data = array();
                $dif_data[0] = $ilk;
                $dif_data[1] = $son;
                foreach ($son as $key => $value) {
                    if ($key == 'Tarih') {
                        $dif_data[2][$key] = "Fark";
                    } else {
                        if ($value != "" && $ilk[$key] != "") {
                            $dif_data[2][$key] = $value - $ilk[$key];
                        } else {
                            $dif_data[2][$key] = "";
                        }
                    }
                }
                return Excel::download(new DeviceDatasExport($dif_data), $devices->name . '_fark.xlsx');
            } else {
                return Excel::download(new DeviceDatasExport($regulardata), $devices->name . '.xlsx');
            }
        } else {
            $count = Cache::remember($countCacheName, 180, function () use ($count) {
                return $count->first()->adet;
            });
            $return['draw'] = $request->draw;
            $return['recordsTotal'] = $count;
            $return['recordsFiltered'] = $count;
            $return['data'] = $regulardata;
            return json_encode($return, 1);
        }
    }
    public function addVirtual($id = null)
    {
        $this->authorize('virtual', app('App\Device'));
        $data = array();
        if (!is_null($id)) {
            $data['device'] = Device::where('company_id', Auth::user()->company_id)->where('id', $id)->first();
        }
        $data['devices'] =  $devices = Device::where('company_id', Auth::user()->company_id)->get();
        return view('voyager::cihazlar.sanal', $data);
    }

    public function addDosab($id = null)
    {
        $this->authorize('dosab', app('App\Device'));
        //Device::dosabData();
        $data = array();
        if (!is_null($id)) {
            $data['device'] = Device::where('company_id', Auth::user()->company_id)->where('id', $id)->first();
        }

        return view('voyager::cihazlar.dosab', $data);
    }

    public function saveDosab(Request $request)
    {

        $this->authorize('dosab', app('App\Device'));
        if (isset($request->id)) {
            $device = Device::where('company_id', Auth::user()->company_id)->where('id', $request->id)->first();
        } else {
            $device = new Device;
        }
        $device->mac = "00:00:00:00:00:01";
        $device->device_id = "EOS_DOSAB_" . $request->device_id . "_" . $request->device_type;
        $device->name = $request->name;
        $device->company_id = Auth::user()->company_id;
        $device->diff_tags = json_encode($request->diff_tags, JSON_UNESCAPED_UNICODE);
        $device->last_at = date('Y-m-d 01:00:00');
        $device->save();
        return back()->with(['message' => "Dosab Sayaç Eklendi", 'alert-type' => 'success']);
    }


    public function addManuel($id = null, Request $request)
    {
        /*$device = new Device;
        $device->mac = "00:00:00:00:00:02";
        $device->device_id= "EOS_MANUEL_". Auth::user()->company_id. '_' . rand(1, 1000000);
        $device->company_id = Auth::user()->company_id;
        $device->last_at= date('Y-m-d 01:00:00');
        $device->save();
        $device->device_id= "EOS_MANUEL_". Auth::user()->company_id. '_' . $device->id;
        $device->save();*/
        // Check permission
        $this->authorize('manuel', app('App\Device'));
        return redirect("/cihazlar/create")->withInput(['mac' => "00:00:00:00:00:02", 'device_id' => "EOS_MANUEL_" . Auth::user()->company_id . '_' . rand(1, 1000000), 'last_at' => date('Y-m-d 01:00:00')]);
    }

    public function addRemote(Request $request)
    {

        $this->authorize('remote', app('App\Device'));
        $tagAccess = TagAccess::where('token', $request->token)->first();
        if ($tagAccess) {
            $tags = json_decode($tagAccess->tags, true);
            $saveTags = array();
            foreach ($tags as $key => $value) {
                $varible = explode('-', $value);
                $tempDevice = Device::find($varible[0]);
                $tempTags =  json_decode($tempDevice->tags);
                $saveTags[$key] = $tempDevice->name . " - " . $tempTags[$varible[1]];
            }
            $device = new Device;
            $device->mac = "00:00:00:00:00:03";
            $device->device_id = "EOS_REMOTE_" . Auth::user()->company_id . '_' . rand(10000, 10000000);
            $device->company_id = Auth::user()->company_id;
            $device->name = Company::find($tagAccess->company_id)->name;
            $device->last_at = date('Y-m-d 01:00:00');
            $device->tags = json_encode($saveTags);
            $device->formula = json_encode(['token_id' => $tagAccess->id, 'token' => $tagAccess->token]);
            $device->save();
            $device->device_id = "EOS_REMOTE_" . Auth::user()->company_id . '_' . $device->id;
            $device->save();
            return redirect("/cihazlar/{$device->id}/edit");
        } else {
            return back()->with(['message' => "Token Bulunamadı", 'alert-type' => 'error']);
        }
    }



    public function saveVirtual(Request $request)
    {

        $this->authorize('virtual', app('App\Device'));
        if (isset($request->id)) {
            $device = Device::where('company_id', Auth::user()->company_id)->where('id', $request->id)->first();
        } else {
            $device = new Device;
        }
        $device->mac = "00:00:00:00:00:00";
        $device->device_id = "EOS_SNL" . date("ymd") . str_pad(rand(0, 999), 3, "0", STR_PAD_LEFT);
        $device->name = $request->name;
        $device->tags = $request->tags;
        $device->formula = $request->formula;
        $device->type = $request->type;
        $device->company_id = Auth::user()->company_id;
        $device->save();
        return redirect()->route("voyager.cihazlar.index")->with(['message' => "Sanal Makine Eklendi", 'alert-type' => 'success']);
        // return back()->with(['message' => "Sanal Makine Eklendi", 'alert-type' => 'success']);
    }
    //***************************************
    //                ______
    //               |  ____|
    //               | |__
    //               |  __|
    //               | |____
    //               |______|
    //
    //  Edit an item of our Data Type BR(E)AD
    //
    //****************************************

    public function edit(Request $request, $id)
    {


        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses($model))) {
                $model = $model->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
                $model = $model->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        // Check permission
        switch ($dataTypeContent->mac) {
            case '00:00:00:00:00:00':
                $this->authorize('virtual',  app('App\Device'));
                break;
            case '00:00:00:00:00:01':
                $this->authorize('dosab',  app('App\Device'));
                break;
            case '00:00:00:00:00:02':
                $this->authorize('manuel',  app('App\Device'));
                break;
            case '00:00:00:00:00:03':
                $this->authorize('remote',  app('App\Device'));
                break;
            default:
                $this->authorize('edit',  app('App\Device'));
                break;
        }

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

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
        switch ($request->mac) {
            case '00:00:00:00:00:00':
                $this->authorize('virtual',  app('App\Device'));
                break;
            case '00:00:00:00:00:01':
                $this->authorize('dosab',  app('App\Device'));
                break;
            case '00:00:00:00:00:02':
                $this->authorize('manuel',  app('App\Device'));
                break;
            case '00:00:00:00:00:03':
                $this->authorize('remote',  app('App\Device'));
                break;
            default:
                $this->authorize('edit',  app('App\Device'));
                break;
        }

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();

        $dataAll = $request->all();
        if (!isset($dataAll['company_id']) || $dataAll['company_id'] ==  Auth::user()->company_id) {
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
    //***************************************
    //
    //                   /\
    //                  /  \
    //                 / /\ \
    //                / ____ \
    //               /_/    \_\
    //
    //
    // Add a new item of our Data Type BRE(A)D
    //
    //****************************************

    public function create(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        switch ($request->mac) {
            case '00:00:00:00:00:00':
                $this->authorize('virtual',  app('App\Device'));
                break;
            case '00:00:00:00:00:01':
                $this->authorize('dosab',  app('App\Device'));
                break;
            case '00:00:00:00:00:02':
                $this->authorize('manuel',  app('App\Device'));
                break;
            case '00:00:00:00:00:03':
                $this->authorize('remote',  app('App\Device'));
                break;
            default:
                $this->authorize('add',  app('App\Device'));
                break;
        }

        $dataTypeContent = (strlen($dataType->model_name) != 0)
            ? new $dataType->model_name()
            : false;

        foreach ($dataType->addRows as $key => $row) {
            $dataType->addRows[$key]['col_width'] = $row->details->width ?? 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'add');

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

    /**
     * POST BRE(A)D - Store data.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();


        // Check permission
        switch ($request->mac) {
            case '00:00:00:00:00:00':
                $this->authorize('virtual',  app('App\Device'));
                break;
            case '00:00:00:00:00:01':
                $this->authorize('dosab',  app('App\Device'));
                break;
            case '00:00:00:00:00:02':
                $this->authorize('manuel',  app('App\Device'));
                break;
            case '00:00:00:00:00:03':
                $this->authorize('remote',  app('App\Device'));
                break;
            default:
                $this->authorize('add',  app('App\Device'));
                break;
        }


        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();

        if (in_array($dataType->model_name, $this->companymodels)) {
            $request->merge(['company_id' => Auth::user()->company_id]);
        }
        if (in_array($dataType->model_name, $this->dashboardmodels)) {
            $request->merge(['user_id' => Auth::user()->id]);
        }
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


    //***************************************
    //                _____
    //               |  __ \
    //               | |  | |
    //               | |  | |
    //               | |__| |
    //               |_____/
    //
    //         Delete an item BREA(D)
    //
    //****************************************

    public function destroy(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        if (!empty($id)) {
            $device = Device::where('id', $id)->first();
        } else {
            $device = (object)['mac' => "yok"];
        }
        // Check permission
        switch ($device->mac) {
            case '00:00:00:00:00:00':
                $this->authorize('virtual',  app('App\Device'));
                break;
            case '00:00:00:00:00:01':
                $this->authorize('dosab',  app('App\Device'));
                break;
            case '00:00:00:00:00:02':
                $this->authorize('manuel',  app('App\Device'));
                break;
            case '00:00:00:00:00:03':
                $this->authorize('remote',  app('App\Device'));
                break;
            default:
                $this->authorize('delete',  app('App\Device'));
                break;
        }


        // Init array of IDs
        $ids = [];
        if (empty($id)) {
            // Bulk delete, get IDs from POST
            $ids = explode(',', $request->ids);
        } else {
            // Single item delete, get ID from URL
            $ids[] = $id;
        }
        foreach ($ids as $id) {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);

            $model = app($dataType->model_name);
            if (!($model && in_array(SoftDeletes::class, class_uses($model)))) {
                $this->cleanup($dataType, $data);
            }
        }

        $displayName = count($ids) > 1 ? $dataType->getTranslatedAttribute('display_name_plural') : $dataType->getTranslatedAttribute('display_name_singular');

        $res = $data->destroy($ids);
        $data = $res
            ? [
                'message'    => __('voyager::generic.successfully_deleted') . " {$displayName}",
                'alert-type' => 'success',
            ]
            : [
                'message'    => __('voyager::generic.error_deleting') . " {$displayName}",
                'alert-type' => 'error',
            ];

        if ($res) {
            event(new BreadDataDeleted($dataType, $data));
        }

        return redirect()->route("voyager.{$dataType->slug}.index")->with($data);
    }
}
