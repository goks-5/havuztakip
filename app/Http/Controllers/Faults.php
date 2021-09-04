<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\User;
use App\Fault;

use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class Faults extends VoyagerBaseController
{
    public function actions(Request $request)
    {
        $this->authorize('browse_admin');
        switch ($request->action) {
   case 'accept':
   $this->authorize('accept', app('App\Fault'));
     if (isset($request->id)) {
         $save['maintainer_id'] = $request->maintainer_id;
         $save['accepted_at'] = date("Y-m-d H:i:s");
         $save['status'] = 'Bekliyor |0|';
         if (Fault::where('id', $request->id)->where('company_id', Auth::user()->company_id)->update($save)) {
             return back()->with(['message' => "Arıza Kabul Edildi", 'alert-type' => 'success']);
         } else {
             return back()->with(['message' => "Arıza Bulunamadı", 'alert-type' => 'error']);
         }
     } else {
         return back()->with(['message' => "Hatalı İstek", 'alert-type' => 'error']);
     }
     break;
     case 'action':
     $this->authorize('accept', app('App\Fault'));
       if (isset($request->id)) {
           $old = Fault::where('id', $request->id)->first();
           $save['status'] = $request->status;
           if ($request->status == 'Onay |1|') {
               $save['finish_at'] = date("Y-m-d H:i:s");
           }
           if (strlen($request->maintainer_note)> 0) {
               $save['maintainer_note'] = $old->maintainer_note . "\n". date("Y-m-d H:i:s") . "\n" . $request->maintainer_note;
           }
           if (Fault::where('id', $request->id)->where('company_id', Auth::user()->company_id)->update($save)) {
               return back()->with(['message' => "Arıza İşlemi Girildi", 'alert-type' => 'success']);
           } else {
               return back()->with(['message' => "Arıza Bulunamadı", 'alert-type' => 'error']);
           }
       } else {
           return back()->with(['message' => "Hatalı İstek ", 'alert-type' => 'error']);
       }
       break;
       case 'close':
       $this->authorize('close', app('App\Fault'));
         if (isset($request->id)) {
             $save['status'] = "Bitti |1|";

             if (Fault::where('id', $request->id)->where('company_id', Auth::user()->company_id)->update($save)) {
                 return back()->with(['message' => "Arıza Kapatıldı", 'alert-type' => 'success']);
             } else {
                 return back()->with(['message' => "Arıza Bulunamadı", 'alert-type' => 'error']);
             }
         } else {
             return back()->with(['message' => "Hatalı İstek ", 'alert-type' => 'error']);
         }
         break;


 }
    }
}
