<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataUpdated;

use App\Device;
use App\CustomReport;
use App\Http\Controllers\VoyagerBaseController;

use TCG\Voyager\Facades\Voyager;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

use App\Exports\CustomReportExport;

class CustomReports extends VoyagerBaseController
{
    public function edit(Request $request, $id)
    {
      $view = 'voyager::custom-reports.edit-add';
        $data['report'] =  CustomReport::where('company_id', Auth::user()->company_id)->where('id', $id)->first();
        $data['devices'] =   Device::where('company_id', Auth::user()->company_id)->get();
        return Voyager::view($view, $data);
    }


    public function excel(Request $request, $id)
    {
        $report =  CustomReport::where('company_id', Auth::user()->company_id)->where('id', $id)->first();
        if(isset($request->date)){
          $date = date('Y-m-d H:i',strtotime($request->date)) ;
        }else{
            $date = date('Y-m-d H:i');
        }
        $datas = json_decode($report->datas, true);
        $excelcikti = array();
        foreach ($datas as $satir => $veriler) {
          foreach ($veriler as $sutun => $veri) {
            switch ($veri['type']) {
              case 'text':
              $excelcikti[$satir][$sutun] = $veri['text'];
                break;
              case 'tag':
                $value = json_decode($veri['value'], true);
                $excelcikti[$satir][$sutun] = DB::select("SELECT device_date_data_12h({$value['device']}, {$value['device_index']},'{$date}') as data")[0]->data;
              break;
              case 'date':
                $excelcikti[$satir][$sutun] = $date;
              break;
              case 'user':
                $excelcikti[$satir][$sutun] = Auth::user()->name;
              break;
            }
          }
        }
          return Excel::download(new CustomReportExport(array_values($excelcikti)), $report->name . date('_Y-m-d_H-i',strtotime($date)) .'.xlsx');

    }
    public function create(Request $request)
    {
        $view = 'voyager::custom-reports.edit-add';

        $data['devices'] =  $devices = Device::where('company_id', Auth::user()->company_id)->get();
        return Voyager::view($view, $data);
    }


    public function store(Request $request)
    {

        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();

                $request->merge(['company_id' => Auth::user()->company_id]);
                $request->merge(['datas' => json_encode($request->datas)]);



        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());


        event(new BreadDataAdded($dataType, $data));

        if (!$request->has('_tagging')) {
            if (auth()->user()->can('browse', $data)) {
                $redirect = redirect()->route("voyager.{$dataType->slug}.index");
            } else {
                $redirect = redirect()->back();
            }

            return $redirect->with([
                    'message'    => __('voyager::generic.successfully_added_new')." {$dataType->getTranslatedAttribute('display_name_singular')}",
                    'alert-type' => 'success',
                ]);
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
    }
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
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
            $request->merge(['datas' => json_encode($request->datas)]);
            $this->insertUpdateData($request, $slug, $dataType->editRows, $data);
        }

        event(new BreadDataUpdated($dataType, $data));

        if (auth()->user()->can('browse', $model)) {
            $redirect = redirect()->route("voyager.{$dataType->slug}.index");
        } else {
            $redirect = redirect()->back();
        }

        return $redirect->with([
            'message'    => __('voyager::generic.successfully_updated')." {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }
}
