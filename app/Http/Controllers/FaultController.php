<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Fault;
use PDF; // Barryvdh/Dompdf facadesini kullanabilmek için

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

    public function store(Request $request)
    {
        Fault::create([
            'equipment_id' => $request->equipment_id,
            'fault_type' => $request->fault_type,
            'fault_code' => $request->fault_code,
            'fault_comment' => $request->fault_comment,
            'reporting_user' => $request->reporting_user,
            'status' => 'Yeni',
        ]);

        return redirect()->back()->with('success', 'Arıza başarıyla eklendi.');
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

    public function show(Request $request, $id)
    {
        $fault = Fault::findOrFail($id);
        
        // Aşağıdaki satır view dosyanızın bulunduğu yolu yansıtmalı
        return view('vendor.voyager.yeni-gelen-is-emirleri.show', compact('fault'));
    }

    public function edit(Request $request, $id)
    {
        $fault = Fault::findOrFail($id);

        // Durum seçenekleri
        $statuses = ['Yeni', 'İşleme alındı', 'Başladı', 'Firma', 'Malzeme', 'Onay', 'Biten'];

        // Arıza tipi seçenekleri
        $fault_types = ['Arıza', 'Bakım', 'Planlı Duruş', 'Montaj', 'ISG', 'Diğer'];

        // Arıza kodu seçenekleri (key: kod, value: açıklama)
        $fault_codes = [
            '100' => 'Mekanik',
            '200' => 'Elektrik',
            '300' => 'Bakım',
            '400' => 'Tesisat',
            '500' => 'Kaynak',
            '600' => 'İnşaat',
            '700' => 'Genel',
            '800' => 'Montaj'
        ];

        // Ekipmanları, fault kaydındaki company_id'ye göre filtrele
        $equipments = \App\Equipment::where('company_id', $fault->company_id)->get();

        return view('vendor.voyager.yeni-gelen-is-emirleri.edit', compact('fault', 'statuses', 'fault_types', 'fault_codes', 'equipments'));
    }

    public function update(Request $request, $id)
    {
        // ID'ye göre ilgili kaydı bulun
        $fault = Fault::findOrFail($id);

        // Formdan gelen verileri kayda uygula (örnek)
        $fault->status = $request->input('status');
        $fault->fault_type = $request->input('fault_type');
        $fault->fault_code = $request->input('fault_code');
        $fault->fault_comment = $request->input('fault_comment');
        $fault->reporting_user = $request->input('reporting_user');
        $fault->finish_at = $request->input('finish_at');
        $fault->maintainer_note = $request->input('maintainer_note');
        // vs. diğer alanlar...

        // Değişiklikleri veritabanına kaydet
        $fault->save();

        // Başarılı güncelleme sonrası bir sayfaya yönlendirin
        // Örneğin "show" sayfasına geri dönmek:
        return redirect()->route('yeni-gelen-is-emirleri.show', $fault->id)
                        ->with('success', 'Kayıt başarıyla güncellendi!');
    }

    public function destroy(Request $request, $id)
    {
        $fault = Fault::findOrFail($id);
        $fault->delete();

        return redirect()->route('yeni-gelen-is-emirleri.browse')
                        ->with('success', 'Kayıt başarıyla silindi!');
    }

    public function pdfView(Request $request, $id)
    {
        $fault = Fault::findOrFail($id);
        // PDF için kullanılacak view. (pdf.blade.php)
        return view('vendor.voyager.yeni-gelen-is-emirleri.pdf', compact('fault'));
    }

    public function pdf($id)
    {
        $fault = Fault::findOrFail($id);
        $pdf = PDF::loadView('vendor.voyager.yeni-gelen-is-emirleri.pdf', compact('fault'));
        return $pdf->download('ariza_' . $fault->id . '.pdf');
    }
    
    public function browse(Request $request)
    {
        $faults = Fault::where('status', 'Yeni')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
        $staffs = \App\Staff::all();

        return view('vendor.voyager.yeni-gelen-is-emirleri.browse', compact('faults', 'staffs'));
    }
    
    public function accept(Request $request, $id)
    {
        // 1. Arızayı bul
        $fault = Fault::findOrFail($id);

        // 2. Formdan gelen staff_id değerini maintainer_id alanına ata
        $fault->maintainer_id = $request->input('staff_id');

        // İsterseniz statüyü de değiştirebilirsiniz (örnek)
        $fault->status = 'Bekliyor |0|';

        // 3. Veritabanına kaydet
        $fault->save();

        // 4. Başarılı işlem sonrası liste sayfasına yönlendir
        return redirect()->route('yeni-gelen-is-emirleri.browse')
                        ->with('success', 'Arıza kabul edildi ve bakımcı atandı.');
    }

    public function process(Request $request, $id)
    {
        $fault = Fault::findOrFail($id);

        // Formdan gelen verileri kaydet
        $fault->status = $request->input('status');
        $fault->maintainer_note = $request->input('comment'); // Örnek: comment'i maintainer_note alanına yazmak
        $fault->save();

        // Başarılı işlem sonrası geri yönlendir
        return redirect()->back()->with('success', 'İşlem kaydedildi!');
    }

}
