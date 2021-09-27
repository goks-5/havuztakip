<?php
namespace App\Http\Controllers;

use App\Dashboard;
use App\Http\Controllers\Dashboards;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Http\Controllers\VoyagerController as Base;

class VoyagerController extends Base
{
    public function index()
    {
        $this->authorize('browse_admin');


        $dashboard = Dashboard::where('user_id', Auth::user()->id)
        ->where('company_id', Auth::user()->company_id)
        ->first();
        if(!isset($dashboard->id)){
          $dashboard = new Dashboard;
          $dashboard->title = "Ana İzleme Ekranı";
          $dashboard->user_id = Auth::user()->id;
          $dashboard->company_id = Auth::user()->company_id;
          $dashboard->save();
        }
        $dash = new Dashboards;
        return $dash->dashboard($dashboard->id);
    }

}
