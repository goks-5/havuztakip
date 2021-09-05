<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Equipment;
use App\Dashboard;
use App\DashboardRow;
use App\DashboardTool;
use App\Tool;
use App\Http\Controllers\VoyagerBaseController;

use Illuminate\Http\Request;

class Dashboards extends VoyagerBaseController
{
    public function index(Request $request)
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
        return $this->dashboard($dashboard->id);

    }

    public function dashboard($dashboard){
      $this->authorize('browse_admin');
      $data = array();
      $boards =Dashboard::where('user_id', Auth::user()->id)
      ->where('company_id', Auth::user()->company_id)
      ->get();
      $board = Dashboard::where('user_id', Auth::user()->id)
      ->where('company_id', Auth::user()->company_id)
      ->where('id',$dashboard)
      ->first();
      if(isset( $board->id)){
        $tools = DashboardTool::where('dashboard_id', $board->id)
        ->orderBy('order')->get();
        $toolSets = Tool::orderBy('order')->get();
        $data['boards'] = $boards;
        $data['board'] = $board;
        $data['tools'] =  $tools;
        $data['toolSets'] =  $toolSets;
        return view('dashboard.dashboard', $data);
      }else{
        abort(403);
      }
    }

    public function ajaxdata(Request $request){

            $this->authorize('browse_admin');
              $request = $request->input();
              if(isset($request['dashboard']) && $request['dashboard'] > 0){
                $tools = Dashboard::select('dashboard_tools.*')
                ->join('dashboard_tools','dashboards.id', '=' , 'dashboard_tools.dashboard_id')
                ->where('dashboards.id', $request['dashboard'])
                ->where('dashboards.user_id', Auth::user()->id)
                ->where('dashboards.company_id', Auth::user()->company_id)
                ->get();
              }else{
                $tools = DashboardRow::select('dashboard_tools.*')
                ->join('dashboard_tools','dashboard_rows.id', '=' , 'dashboard_tools.row_id')
                ->where('dashboard_rows.user_id', Auth::user()->id)
                ->where('company_id', Auth::user()->company_id)
                ->get();
              }

 $DashboardTool = new DashboardTool();
      return $DashboardTool->ajaxdata($tools);
    }
}
