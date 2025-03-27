<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Fault;

class FaultController extends Controller
{
    public function index(Request $request)
    {
        $query = Fault::where('status', 'Onay |1|')->orderBy('created_at', 'desc');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('fault_type', 'like', "%{$search}%")
                  ->orWhere('fault_code', 'like', "%{$search}%")
                  ->orWhere('fault_comment', 'like', "%{$search}%")
                  ->orWhere('reporting_user', 'like', "%{$search}%")
                  ->orWhere('maintainer_note', 'like', "%{$search}%")
                  ->orWhereHas('staff', function ($staffQuery) use ($search) {
                      $staffQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('equipment', function ($equipQuery) use ($search) {
                      $equipQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $faults = $query->paginate(100);

        return view('vendor.voyager.arizalar.browse', compact('faults'));
    }

    public function closeSelected(Request $request)
    {
        $ids = explode(',', $request->input('selected_ids'));

        Fault::whereIn('id', $ids)->update([
            'status' => 'Bitti |1|'
        ]);

        return redirect()->back()->with([
            'message' => 'Seçilen arızalar başarıyla kapatıldı.',
            'alert-type' => 'success'
        ]);
    }

    // Eğer diğer işlemleri de kullanıyorsan actions() methodunu da burada bulundurabilirsin.
    public function actions(Request $request)
    {
        if ($request->action === 'close_selected') {
            $ids = explode(',', $request->input('selected_ids'));
            Fault::whereIn('id', $ids)->update(['status' => 'Bitti |1|']);

            return back()->with([
                'message' => 'Arızalar kapatıldı.',
                'alert-type' => 'success'
            ]);
        }

        return back();
    }
}
