<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Maintenance;
use App\MaintenanceWeek;
use App\MaintenanceTask;
use App\Week;
use App\Equipment;
use App\Staff;
use DateTime;
use DateInterval;
use DatePeriod;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class Maintenances extends VoyagerBaseController
{
    public function tasks($status = 'buhafta')
    {
        $this->authorize('tasks', app('App\Maintenance'));
        $maintances = Maintenance::where('company_id', Auth::user()->company_id)->get();
        $data = array();
        $data['tasks'] = array();
        foreach ($maintances as $maintance) {
            $equipment = Equipment::where('id', $maintance->equipment_id)->first();
            $MaintenanceWeek = MaintenanceWeek::join('weeks', 'weeks.id', '=', 'maintenance_weeks.week_id')->where('maintenance_weeks.maintenance_id', $maintance->id)->get();

            $weeks = array();
            foreach ($MaintenanceWeek as $week) {
                $weeks[] = $week->week_id;
            }

            if (empty($equipment->kurulum_tarihi)) {
                $start    = new DateTime("last monday " . $equipment->created);
            } else {
                $start    = new DateTime("last monday " .$equipment->kurulum_tarihi);
            }

            $end      = new DateTime('+1 years');
            switch ($status) {
              case 'tamamlanan':
                $end      = new DateTime('monday +2 week');
              break;
              case 'bekleyen':
                $end      = new DateTime('monday +2 week');
              break;
              case 'guncel':
                $end      = new DateTime('monday +1 week');
              break;
              case 'gelecek':
                $start      = new DateTime('monday +1 week');
                $end      = new DateTime('+1 years');
              break;

            }




            $interval = new DateInterval('P1W');
            $period   = new DatePeriod($start, $interval, $end);

            $MaintenanceTasks = MaintenanceTask::select('maintenance_tasks.*', 'staff.name')->where('maintenance_id', $maintance->id)->leftjoin('staff', 'staff.id', '=', 'maintenance_tasks.task_user_id')->get();
            $tasks= array();
            foreach ($MaintenanceTasks as $task) {
                $tasks[$task->task_week_id] = $task;
            }

            foreach ($period as $date) {
                if (in_array(ltrim($date->format('W'), '0'), $weeks)) {
                    if (!isset($tasks[$date->format('oW')])) {
                        $tasks[$date->format('oW')] = null;
                        $fstatus = "Bekliyor";
                    } else {
                        $fstatus = $tasks[$date->format('oW')]->status;
                    }

                    switch ($status) {
                      case 'tamamlanan':
                      if ($fstatus == 'Tamamlandı') {
                          $data['tasks'][] = ['equipment'=>$equipment,'maintance'=>$maintance,'id' =>$date->format('oW'),'tarih'=>$date->format('Y-m-d'),'task'=>$tasks[$date->format('oW')]];
                      }
                      break;
                      case 'bekleyen':
                      if ($fstatus != 'Tamamlandı' && $fstatus != 'Bekliyor') {
                          $data['tasks'][] = ['equipment'=>$equipment,'maintance'=>$maintance,'id' =>$date->format('oW'),'tarih'=>$date->format('Y-m-d'),'task'=>$tasks[$date->format('oW')]];
                      }
                      break;
                      case 'guncel':
                        if ($fstatus == 'Bekliyor') {
                            $data['tasks'][] = ['equipment'=>$equipment,'maintance'=>$maintance,'id' =>$date->format('oW'),'tarih'=>$date->format('Y-m-d'),'task'=>$tasks[$date->format('oW')]];
                        }
                      break;
                      case 'gelecek':
                      $data['tasks'][] = ['equipment'=>$equipment,'maintance'=>$maintance,'id' =>$date->format('oW'),'tarih'=>$date->format('Y-m-d'),'task'=>$tasks[$date->format('oW')]];

                      break;

                    }


                    if ($status == 'complate') {
                    } else {
                    }
                }
            }
        }
        $data['culumns'] = ['Id','Ekipman','Envanter Kodu','Bakım','Planlanan Tarih','Yapılan Tarih','Bakımcı','Durum' ];
        $data['status'] =$status;
        return view('bakimlar.tasks', $data);
    }

    public function addEdit(Request $request)
    {
        $this->authorize('tasks', app('App\Maintenance'));
        $data['maintenance'] = Maintenance::where('company_id', Auth::user()->company_id)
      ->where('id', $request->maintenance_id)
      ->first();
        $data['task_week_id'] = $request->task_week_id;
        $data['equipment'] = Equipment::where('id', $request->equipment_id)->first();
        $data['staffs'] = Staff::where('company_id', Auth::user()->company_id)->where('active', 1)->get();
        $data['task'] = MaintenanceTask::where('maintenance_id', $data['maintenance']->id)
      ->where('equipment_id', $data['equipment']->id)
      ->where('task_week_id', $request->task_week_id)
      ->first();
        if (isset($request->save)) {
            if (isset($data['task']->file)) {
                $file = $data['task']->file;
            }else{$file = "";}

            if ($request->hasFile('file')) {
                $request->validate([
            'file' => 'mimes:pdf,doc,docx,jpeg,jpg,png|max:1024'  ], [
            'file.mimes'=>'Pdf Word Belgesi veya jpg,png Resim Dosyaları Kabul Edilir',
            'file.max'=>'Dosya 1Mb dan Küçük Olmalı'
          ]);

                $fileName = md5(time() * rand(1, 1000)) .'.'. $request->file->extension();
                $path = 'bakimlar/'.date('FY');
                $filea["original_name"] = $request->file->getClientOriginalName();
                $filea["download_link"] =$path . '/' .$fileName;
                $request->file->move(storage_path("app/public/".$path), $fileName);
                $file = json_encode($filea);
            }
            $save = $request->all();
            $save['file']=$file;
            $save['user_id']= Auth::user()->id;
            unset($save['_token']);
            unset($save['save']);
            if (isset($data['task']->id)) {
                MaintenanceTask::where('id', $data['task']->id)->update($save);
            } else {
                MaintenanceTask::insert($save);
            }
            ;
            return back();
        } else {
            return view('bakimlar.addEdit', $data);
        }
    }
}
