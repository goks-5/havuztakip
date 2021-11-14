<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;

use App\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Events\BreadDataDeleted;

class Companies extends VoyagerBaseController
{

  public function switch($id = null)
  {

    if (!Auth::guest()) {
      $user_id = Auth::user()->id;
      $tx = DB::table("company_users")->where("user_id", $user_id)->get();;
      foreach ($tx as  $company) {
        if (is_numeric($id) && $company->company_id == $id) {
          User::where('id', $user_id)->update(['company_id' =>  $company->company_id]);
        //  Auth::user()->company_id = $company->company_id;
          //  Auth::user()->company = Company::where('id', $company->company_id)->first();
          return redirect()->route('voyager.dashboard');
        }
        $data['companies'][$company->company_id] = Company::where('id', $company->company_id)->first();
      }
      return view('voyager::firmalar.switch', $data);
    } else {
      return redirect()->route('voyager.login');
    }
  }

  public function destroy(Request $request, $id)
  {
      $slug = $this->getSlug($request);

      $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

      // Check permission
      $this->authorize('delete', app($dataType->model_name));

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
      DB::table("company_users")->whereIn('company_id' ,$ids)->delete();
      return redirect()->route("voyager.{$dataType->slug}.index")->with($data);
  }


}
