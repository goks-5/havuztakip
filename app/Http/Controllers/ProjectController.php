<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\Offer; // Offer modelini de kullanmak için

class ProjectController extends Controller
{
    public function store(Request $request)
    {
        // Validasyon
        $request->validate([
            'offer_id'             => 'required|integer',
            'kabul_bedeli'         => 'required|numeric|min:1',
            'caliscak_kisi_sayisi' => 'required|integer|min:1',
            'project_end_date'     => 'required|date',
        ]);

        // Proje kaydı oluştur
        $project = \App\Project::create([
            'offer_id'             => $request->offer_id,
            'kabul_bedeli'         => $request->kabul_bedeli,
            'caliscak_kisi_sayisi' => $request->caliscak_kisi_sayisi,
            'proje_bitis_tarihi'   => $request->project_end_date,
        ]);

        // İsterseniz Offer tablosunda is_editable değerini 2 (Proje oluşturuldu) yapabilirsiniz
        \App\Offer::where('id', $request->offer_id)->update(['is_editable' => 2]);

        // Başarılı işlem sonrası geri yönlendir
        return redirect()->back()->with('success', 'Proje başarıyla oluşturuldu!');
    }

    public function view($offerId)
{
    // 1) project tablosundan, offer_id= $offerId olan kaydı bul
    $project = Project::where('offer_id', $offerId)->firstOrFail();

    // 2) offer tablosundan kaydı bul
    $offer = Offer::findOrFail($offerId);

    // 3) bills tablosundan, project_id = $project->id olan faturaları çek
    $bills = \App\Bills::where('project_id', $project->id)->get();

    // 4) view dosyasına verileri gönder
    return view('vendor.voyager.teklif-hazirla.project', compact('project', 'offer', 'bills'));
}

}
