<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Http\Request;
use App\Device;
use App\CompanySetting;

use App\Http\Controllers\VoyagerBaseController;

class CompanySettings extends VoyagerBaseController
{
    public function index(Request $request)
    {
          $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();



        $dataTypeContent = CompanySetting::find(Auth::user()->company_id);
        if ($dataTypeContent == null) {
            $dataTypeContent = app($dataType->model_name);
        }


        $this->authorize('browse', $dataTypeContent);


        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }

        $this->removeRelationshipField($dataType, 'edit');

        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }
    public function edit(Request $request,$id)
    {
        Device::dosabData();
    }


}
