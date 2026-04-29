<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bills;

class BillsController extends Controller
{
    public function store(Request $request)
    {
        // Gelen verileri doğrula
        $request->validate([
            'project_id'      => 'required|integer',
            'fatura_adi'      => 'required|string|max:255',
            'fatura_numarasi' => 'required|integer|min:0',
            'fatura_bedeli'   => 'required|numeric|min:0',
            'tedarikci'       => 'required|string|max:255',
            'para_birimi'     => 'required|in:TRY,USD,EUR', // Yeni eklenen alan
        ]);

        // Yeni fatura kaydını oluştur
        Bills::create([
            'project_id'      => $request->project_id,
            'fatura_adi'      => $request->fatura_adi,
            'fatura_numarasi' => $request->fatura_numarasi,
            'fatura_bedeli'   => $request->fatura_bedeli,
            'tedarikci'       => $request->tedarikci,
            'para_birimi'     => $request->para_birimi, // Yeni eklenen alanın kaydı
        ]);

        // Başarılı işlem sonrası geri yönlendir
        return redirect()->back()->with('success', 'Fatura başarıyla kaydedildi!');
    }
}
