<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dashboard;
use App\DashboardRow;
use App\DashboardTool;
use App\Device;
use App\DeviceData;
use App\ProductionTag;
use App\CompanySetting;
use App\Tool;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class Ajax extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function AutoComplate(Request $request)
    {
        $this->authorize('browse_admin');
        $row =  DB::table("data_rows")->where("id", $request->input('id'))->first();
        $field = $row->field;
        if ($field == $request->input('field')) {
            $rule = $request->input('rule');
            if (strpos($row->details, 'company_id') !== false) {
                $rule['company_id'] = Auth::user()->company_id;
            }
            $where = array();
            foreach ($rule as $key => $value) {
                $where[] = [$key, trim($value)];
            }
            $where[] = [$field, 'like', $request->input('query') . '%'];
            $table = DB::table('data_types')->where('id', $row->data_type_id)->first('name');
            $result =  DB::table($table->name)->where($where)->groupBy($field)->orderBy($field)->get($field);
            $return = array();
            foreach ($result as  $value) {
                $return[] = $value->$field;
            }
            return json_encode($return);
        } else {
            return false;
        }
    }
    public function deviceOnline()
    {
        $this->authorize('browse_admin');
        $devices = DB::table('devices')
            ->where('company_id', Auth::user()->company_id)
            ->get(['id', 'mac', 'name', 'tags', 'last_data', 'last_at', 'tags_last_change']);
        $timeout1 =  setting('device.ofline') * 60;
        $timeout2 =  setting('device.oflinesayac');
        $timeout3 =  setting('device.tag') * 60;
        $cikti["ofline"] = 0;
        $cikti['deviceCount'] = count($devices);
        $cikti['pointCount'] = 0;
        $cikti['oflineCount'] = 0;
        $cikti['oflineDevices'] =  array();
        $cikti['changeTagsCount'] =  0;
        $cikti['changeTags'] =  array();

        foreach ($devices as $device) {
            $tags = [];
            $changeTags = [];
            if (!is_null($device->tags)) {
                $tags = array_filter(json_decode($device->tags, true), function ($k) {
                    return $k < '1000';
                }, ARRAY_FILTER_USE_KEY);
            }



            $cikti['pointCount']  += count($tags);
            if ($device->mac == '00:00:00:00:00:01') {
                $timeout = $timeout2;
            } else {
                $timeout = $timeout1;
            }
            if (strtotime($device->last_at) + $timeout < strtotime('now') && $device->mac != '00:00:00:00:00:02') {
                ++$cikti['oflineCount'];
                $cikti['oflineDevices'][] = $device;
                $cikti["ofline"] = 1;
            }
            if (!is_null($device->tags)) {
                $changeTags = array_filter(json_decode($device->tags, true), function ($k) {
                    return $k >= '1000';
                }, ARRAY_FILTER_USE_KEY);
            }
            $changeAt = json_decode($device->tags_last_change, true);
            if (!is_array($changeAt)) {
                $changeAt = array();
            }
            $changeTags = array_replace($changeTags, $changeAt);
            foreach ($changeTags as $tagkey => $value) {
                if (strtotime($value) + $timeout3 < strtotime('now')) {
                    ++$cikti['changeTagsCount'];
                    $cikti['changeTags'][] = ['name' => $device->name, 'tag' => $tags[$tagkey - 1000], 'last_change' => $value];
                    $cikti["ofline"] = 1;
                }
            }
        }
        return json_encode($cikti);
    }

    public function DeviceList()
    {
        $this->authorize('browse_admin');
        $devices = DB::table('devices')->where('company_id', Auth::user()->company_id)->get(['id', 'name', 'tags', 'last_data', 'last_at']);
        return json_encode($devices);
    }

    public function dahboardTool(Request $request)
    {
        $this->authorize('browse_admin');
        //  $companysobj = DB::table('company_users')->where('user_id', Auth::user()->id)->get('company_id');
        $companys = array();
        // foreach ($companysobj as $value) {
        //   $companys[] = $value->company_id;
        // }
        //
        // $companynamesobj = DB::table('companies')->whereIn('id', $companys)->get();
        $companynames = array();
        // foreach ($companynamesobj as $value) {
        //   $companynames[$value->id] = $value->name;
        // }

        $request = $request->input();

        if (isset($request['row'])) {
            $row = DashboardRow::where('company_id', Auth::user()->company_id)
                ->where('user_id', Auth::user()->id)
                ->where('id', $request['row'])
                ->first();
        }

        if (isset($request['tool'])) {
            $tool = DashboardTool::join('dashboard_rows', 'dashboard_rows.id', '=', 'dashboard_tools.row_id')
                ->where('company_id', Auth::user()->company_id)
                ->where('user_id', Auth::user()->id)
                ->where('dashboard_tools.id', $request['tool'])
                ->first('dashboard_tools.*');
        }

        switch ($request['action_type']) {
            case 'edit_cell':
                if (isset($row->id)) {
                    $data['title'] = $row->{'title_' . $request['index']};
                    $data['color'] = $row->{'color_' . $request['index']};
                    $data['row'] = $row->id;
                    $data['index'] = $request['index'];
                    if (isset($request['save'])) {
                        $row->{'title_' . $request['index']} = $request['title'];
                        $row->{'color_' . $request['index']} = $request['color'];
                        $row->save();
                        return redirect(route('voyager.dashboard'));
                    }
                } else {
                    return 'Satır Bulunamadı';
                }
                break;
            case 'add_tool':
                if (isset($row->id)) {
                    $data['row'] = $row->id;
                    $data['index'] = $request['index'];
                    $data['type'] = $request['type'];
                    $data['devices'] = DB::table('devices')->where('company_id',  Auth::user()->company_id)->get(['id', 'mac', 'company_id', 'name', 'tags', 'last_data', 'last_at']);
                    $data['companys'] = $companynames;
                    if (isset($request['save'])) {
                        if (isset($request['setting']['device'])) {
                            $deviceset = json_decode($request['setting']['device'], true);
                            $request['setting']['device'] = $deviceset['device'];
                            $request['setting']['device_index'] = $deviceset['device_index'];
                        }
                        if (isset($request['setting']['devices'])) {
                            foreach ($request['setting']['devices'] as $device) {
                                $deviceset = json_decode($device, true);
                                $devices[] = ['device' => $deviceset['device'], 'device_index' => $deviceset['device_index']];
                            }
                            $request['setting']['devices'] = $devices;
                        }
                        $save = new DashboardTool;
                        if (isset($request['setting'])) {
                            $save->settings = json_encode($request['setting']);
                        }

                        $save->row_id = $row->id;
                        $save->row_index =  $request['index'];
                        $save->type =  $request['type'];
                        $save->save();
                        return redirect(route('voyager.dashboard'));
                    }
                } else {
                    return 'Satır Bulunamadı';
                }
                break;
            case 'edit_tool':
                if (isset($tool->id)) {
                    $data['row'] = $tool->row_id;
                    $data['index'] = $tool->row_index;
                    $data['type'] = $tool->type;
                    $data['tool'] = $tool->id;
                    $data['settings'] = json_decode($tool->settings, true);
                    $data['devices'] = DB::table('devices')->where('company_id', Auth::user()->company_id)->get(['id', 'mac', 'company_id', 'name', 'tags', 'last_data', 'last_at']);
                    $data['companys'] = $companynames;
                    if (isset($request['save'])) {
                        if (isset($request['setting']['device'])) {
                            $deviceset = json_decode($request['setting']['device'], true);
                            $request['setting']['device'] = $deviceset['device'];
                            $request['setting']['device_index'] = $deviceset['device_index'];
                        }
                        if (isset($request['setting']['devices'])) {
                            foreach ($request['setting']['devices'] as $device) {
                                $deviceset = json_decode($device, true);
                                $devices[] = ['device' => $deviceset['device'], 'device_index' => $deviceset['device_index']];
                            }
                            $request['setting']['devices'] = $devices;
                        }

                        DashboardTool::where('id', $tool->id)
                            ->update(['settings' => json_encode($request['setting'])]);
                        return redirect(route('voyager.dashboard'));
                    }
                } else {
                    return 'Araç Bulunamadı';
                }
                break;
            case 'delete_tool':
                if (isset($tool->id)) {
                    DashboardTool::destroy($tool->id);
                    return redirect()->back()->withSuccess('Araç Silindi');
                } else {
                    return redirect()->back()->withWarning('Araç Bulunamadı');
                }
                break;
            case 'delete_row':
                if (isset($row->id)) {
                    DashboardRow::destroy($row->id);
                    return redirect()->back()->withSuccess('Satır Silindi');
                } else {
                    return redirect()->back()->withWarning('Satır Bulunamadı');
                }
                break;

            case 'add_row':
                $data['row_type'] = 'type_1';
                if (isset($request['save'])) {
                    $save = new DashboardRow;
                    $save->user_id = Auth::user()->id;
                    $save->company_id = Auth::user()->company_id;
                    $save->row_type = $request['row_type'];
                    $save->save();
                    return redirect(route('voyager.dashboard'));
                }
                break;
            case 'edit_row':
                if (isset($row->id)) {
                    $data['row_type'] = $row->row_type;
                    $data['row'] = $row->id;
                    if (isset($request['save'])) {
                        $row->row_type = $request['row_type'];
                        $row->save();
                        return redirect(route('voyager.dashboard'));
                    }
                } else {
                    return 'Araç Bulunamadı';
                }

                break;
        }

        return view('ajax.dashboard.' . $request['action_type'], $data);
    }

    public function calculate(Request $request){
        $setting = CompanySetting::select('day_start_hour', 'week_start_day', 'month_start_day')->find(Auth::user()->company_id);
        $tag = $tag2 = $request->tag;

        $did = "";          
        $basla = false;  
        $devices = array();
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

        $device_data = DB::table('devices')->whereIn('id', $devices)->get();

        $degistir = array();
        $degistir2 = array();
        foreach ($device_data as $data) {
            $lastdata = json_decode($data->last_data, true);
            $tags = json_decode($data->tags, true);

            if (is_array($lastdata)) {
                foreach ($lastdata as $key => $ld) {
                    $degistir["[" . $data->id . "_" . $key . "]"] = $ld;
                    $degistir2["[" . $data->id . "_" . $key . "]"] =  "[" . $data->name . "_" . $tags[$key] . "]" ;
                }
            }
        }
        foreach ($degistir as $key => $value) {
            $tag = str_replace($key, $value, $tag);
        }
        foreach ($degistir2 as $key => $value) {
            $tag2 = str_replace($key, $value, $tag);
        }
        try {
           $return = Device::calculate($tag,$setting);
        } catch (\Throwable $th) {
            $return = $th->getMessage();
        }

        return $tag . " = ". $return . "<br> $tag2";
          
    }


    public function dashboardTagsAdd(Request $request)
    {
        $this->authorize('browse_admin');
        /*if (isset($request['data_tags'])) {
      foreach ($request['data_tags'] as $device) {
          $deviceset = json_decode($device, true);
          $data_tags[] =['device'=>$deviceset['device'],'device_index'=>$deviceset['device_index']];
      }
  }*/

        if ($request['name'] == 'BITIR') {
            return back()->with(['message' => "Etikette BITIR ismini kullanamazsınız", 'alert-type' => 'error']);
        }
        if ($request['name'] == '') {
            return back()->with(['message' => "Etikette ismi boş", 'alert-type' => 'error']);
        }
        if ($request['tags_group'] == '') {
            return back()->with(['message' => "Makine seçilmemiş", 'alert-type' => 'error']);
        }





        $save = new ProductionTag;
        $save->data_tags = $request['data_tags'];
        $save->name = $request['name'];
        $save->start_time =  date('Y-m-d H:i:s');
        $save->created_at =  date('Y-m-d H:i:s');
        $save->company_id = Auth::user()->company_id;
        $save->tags_group = $request['tags_group'];
        $save->save();
        return redirect(route('voyager.dashboard'));
    }

    public function dashboardTagend(Request $request)
    {
        $this->authorize('browse_admin');
        $companysobj = DB::table('company_users')->where('user_id', Auth::user()->id)->get('company_id');
        $companys = array();
        foreach ($companysobj as $value) {
            $companys[] = $value->company_id;
        }

        $row =  ProductionTag::where("id", $request->input('id'))->whereIn("company_id", $companys)->first();

        $row->end_time =  date('Y-m-d H:i:s');
        $row->save();
        return redirect(route('voyager.dashboard'));
    }

    public function toolStyle(Request $request)
    {

        $tool = DashboardTool::find($request->id);
        if ($tool) {
            $board = Dashboard::where('user_id', Auth::user()->id)->where('id', $tool->dashboard_id)->first();
            if ($board) {
                $tool->style = $request->style;
                $tool->save();
                return 'OK';
            }
        }

        abort(403, "Bu işleme Yetkiniz Yok");
    }


    public function boardAction(Request $requestt)
    {
        $this->authorize('browse_admin');
        //  $companysobj = DB::table('company_users')->where('user_id', Auth::user()->id)->get('company_id');
        $companys = array();
        // foreach ($companysobj as $value) {
        //   $companys[] = $value->company_id;
        // }
        //
        // $companynamesobj = DB::table('companies')->whereIn('id', $companys)->get();
        $companynames = array();
        // foreach ($companynamesobj as $value) {
        //   $companynames[$value->id] = $value->name;
        // }

        $request = $requestt->input();

        if (isset($request['board'])) {
            $board = Dashboard::where('company_id', Auth::user()->company_id)
                ->where('user_id', Auth::user()->id)
                ->where('id', $request['board'])
                ->first();
        }

        if (isset($request['tool'])) {

            $tool = DashboardTool::join('dashboards', 'dashboards.id', '=', 'dashboard_tools.dashboard_id')
                ->where('company_id', Auth::user()->company_id)
                ->where('user_id', Auth::user()->id)
                ->where('dashboard_tools.id', $request['tool'])
                ->first('dashboard_tools.*');
        }

        switch ($request['action_type']) {

            case 'add_tool':
                if (isset($board->id)) {
                    $data['route'] = route('boardAction');
                    $data['board'] = $board->id;
                    $data['type'] = $request['type'];
                    $data['devices'] = DB::table('devices')->where('company_id',  Auth::user()->company_id)->get(['id', 'mac', 'company_id', 'name', 'tags', 'last_data', 'last_at']);
                    $data['companys'] = $companynames;
                    if (isset($request['save'])) {
                        if (isset($request['setting']['device'])) {
                            $deviceset = json_decode($request['setting']['device'], true);
                            if (isset($deviceset['device'])) {
                            $request['setting']['device'] = $deviceset['device'];
                            $request['setting']['device_index'] = $deviceset['device_index'];
                            }
                        }
                        if (isset($request['setting']['devices'])) {
                            foreach ($request['setting']['devices'] as $device) {
                                $deviceset = json_decode($device, true);
                                $devices[] = ['device' => $deviceset['device'], 'device_index' => $deviceset['device_index']];
                            }
                            $request['setting']['devices'] = $devices;
                        }
                        $save = new DashboardTool;
                        if ($requestt->hasFile('backgroud')) {

                            $requestt->validate(['backgroud' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048']);
                            $imageName =   time() . rand() . '.' . $requestt->backgroud->extension();
                            $requestt->backgroud->move(public_path('images/backgroud'), $imageName);
                            $request['setting']['css']['background-image'] = "url('../images/backgroud/$imageName')";
                            $request['setting']['css']['background-size'] = 'cover';
                            $request['order'] = 0;
                            $save->style =   "width: 1200px; height: 570px;left: 36px; top: 85px;";
                        }

                        if (isset($request['setting'])) {
                            $save->settings = json_encode($request['setting']);
                        }

                        $save->order = isset($request['order']) ? $request['order'] : 999;

                        $save->dashboard_id = $board->id;
                        $save->type =  $request['type'];
                        $save->save();
                        return redirect()->back()->with(['message' => "Araç Eklendi", 'alert-type' => 'success']);
                    }
                } else {
                    return redirect()->back()->with(['message' => "DashBoard Bulunamadı", 'alert-type' => 'warning']);
                }
                break;
            case 'edit_tool':
                if (isset($tool->id)) {
                    $data['route'] = route('boardAction');
                    $data['type'] = $tool->type;
                    $data['tool'] = $tool->id;
                    $data['settings'] = json_decode($tool->settings, true);
                    $data['devices'] = DB::table('devices')->where('company_id', Auth::user()->company_id)->get(['id', 'mac', 'company_id', 'name', 'tags', 'last_data', 'last_at']);
                    $data['companys'] = $companynames;
                    if (isset($request['save'])) {
                        if (isset($request['setting']['device'])) {
                            $deviceset = json_decode($request['setting']['device'], true);
                            if (isset($deviceset['device'])) {
                            $request['setting']['device'] = $deviceset['device'];
                            $request['setting']['device_index'] = $deviceset['device_index'];
                            }
                        }
                        if (isset($request['setting']['devices'])) {
                            foreach ($request['setting']['devices'] as $device) {
                                $deviceset = json_decode($device, true);
                                $devices[] = ['device' => $deviceset['device'], 'device_index' => $deviceset['device_index']];
                            }
                            $request['setting']['devices'] = $devices;
                        }
                        if ($requestt->hasFile('backgroud')) {

                            $requestt->validate(['backgroud' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048']);
                            $imageName =   time() . rand() . '.' . $requestt->backgroud->extension();
                            $requestt->backgroud->move(public_path('images/backgroud'), $imageName);
                            $request['setting']['css']['background-image'] = "url('../images/backgroud/$imageName')";
                            $request['setting']['css']['background-size'] = 'cover';
                        }
                        if (!isset($request['setting'])) {
                            $request['setting'] = null;
                        }
                        DashboardTool::where('id', $tool->id)
                            ->update(['settings' => json_encode($request['setting']), 'order' => isset($request['order']) ? $request['order'] : 999]);
                        return redirect()->back()->with(['message' => "Araç Düzenlendi", 'alert-type' => 'success']);
                    }
                } else {
                    return redirect()->back()->with(['message' => "Araç Bulunamadı", 'alert-type' => 'warning']);
                }
                break;
            case 'addBoard':
                if (isset($request['title'])) {
                    $dashboard = new Dashboard;
                    $dashboard->title = $request['title'];
                    $dashboard->user_id = Auth::user()->id;
                    $dashboard->company_id = Auth::user()->company_id;
                    $dashboard->save();
                    return redirect(route('dashboardnew', $dashboard->id))->with(['message' => "İzleme Ekranı Eklendi", 'alert-type' => 'success']);
                } else {
                    return redirect()->back()->with(['message' => "Araç Bulunamadı", 'alert-type' => 'warning']);
                }
                break;
            case 'changeBoardName':

                $dashboard = Dashboard::where('user_id', Auth::user()->id)
                    ->where('id', $request['id'])
                    ->first();
                if (isset($dashboard->id)) {
                    $dashboard->title = $request['title'];
                    $dashboard->save();
                    return "OK";
                } else {
                    abort(403);
                }
                break;
            case 'delete_board':

                $dashboard = Dashboard::where('user_id', Auth::user()->id)
                    ->where('id', $request['board'])
                    ->first();
                if (isset($dashboard->id)) {
                    DashboardTool::where('dashboard_id', $dashboard->id)->delete();
                    $dashboard->delete();
                    return redirect('/')->with(['message' => "İzleme Ekranı Silindi", 'alert-type' => 'success']);
                } else {
                    return redirect()->back()->with(['message' => "Ekran Bulunamadı", 'alert-type' => 'warning']);
                }
                break;

            case 'delete_tool':
                if (isset($tool->id)) {
                    DashboardTool::destroy($tool->id);
                    return redirect()->back()->with(['message' => "Araç Silindi", 'alert-type' => 'success']);
                } else {
                    return redirect()->back()->with(['message' => "Araç Bulunamadı", 'alert-type' => 'warning']);
                }
                break;
        }

        return view('ajax.dashboard.' . $request['action_type'], $data);
    }



    public function manuelAjax(Request $request)
    {

        $tool = DashboardTool::find($request->tool_id);
        if ($tool) {
            $settings = json_decode($tool->settings, true);
            $device = Device::where('company_id', Auth::user()->company_id)->where('id', $settings['device'])->first();
            if ($device) {
                $value = $request->status == 'on' ? $settings['onValue'] : $settings['offValue'];  
                $time = date('Y-m-d H:i');
                $deviceData = new DeviceData;
                $deviceData->device_id = $settings['device'];
                $deviceData->data_id = $settings['device_index'];
                $deviceData->value = $value;
                $deviceData->created_at = $time;
                $deviceData->save();
                $device->last_at = $time;
                $lastdata = array();
                if (!empty($device->last_data)) {
                    $lastdata = json_decode($device->last_data, true);
                }
                $replace = array_replace($lastdata, [$settings['device_index']=> $value]);
                ksort($replace);
                $device->last_data = json_encode($replace);
                $device->save();
                dump($replace);

            }
        }
    }
}
