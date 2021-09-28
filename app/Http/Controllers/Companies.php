<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;

use App\Company;

use App\Http\Controllers\VoyagerBaseController;

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
}
