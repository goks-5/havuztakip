<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Offer; // Offer modelini bağlayın
use Carbon\Carbon; // Tarih işlemleri için
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use App\Firm; 
use App\Mail\OfferMail;
use Illuminate\Support\Facades\Mail;

class OfferController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Doğrulama
            $data = $request->validate([
                'title' => 'required',
                'demand_no' => 'required|string|max:255',
                'delivery_date' => 'required|date',
                'company' => 'required|string|max:255', 
                'person_name' => 'required|string',
                'person_email' => 'required|email',
                'currency' => 'required',
                'description' => 'required|array',
                'description.*' => 'required|string',
                'quantity' => 'required|array',
                'quantity.*' => 'required|numeric|min:1',
                'unit_price' => 'required|array',
                'unit_price.*' => 'required|numeric|min:0',
                'total_price' => 'required|array',
                'total_price.*' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
            ]);
    
            // Teklif numarası oluşturma
            $data['offer_no'] = now()->format('YmdHis');
    
             // Eğer notes boş veya null geliyorsa varsayılan değer ata
            $data['notes'] = $request->input('notes', '');

            // Her bir alanı virgülle birleştir
            $description = implode(',', $data['description']);
            $quantity = implode(',', $data['quantity']);
            $unit_price = implode(',', $data['unit_price']);
            $total_price = implode(',', $data['total_price']);
            $total = array_sum($data['total_price']); // Total hesaplama
    
            Log::info('Imploded Description:', [$description]);
            Log::info('Imploded Quantity:', [$quantity]);
            Log::info('Imploded Unit Price:', [$unit_price]);
            Log::info('Imploded Total Price:', [$total_price]);                    
    
            // Tek bir kayıt oluştur
            Offer::create([
                'offer_no' => $data['offer_no'],
                'demand_no' => $data['demand_no'],
                'title' => $data['title'],
                'delivery_date' => $data['delivery_date'],
                'company' => $data['company'],
                'person_name' => $data['person_name'],
                'person_email' => $data['person_email'],
                'currency' => $data['currency'],
                'explanation' => $description,
                'piece' => $quantity,
                'unit_price' => $unit_price,
                'total_price' => $total_price,
                'total' => $total,
                'notes' => $data['notes'],
                'created_at' => now(),
            ]);            
    
            return redirect()->back()->with('success', 'Kayıt başarıyla eklendi.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Errors:', $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Offer Kayıt Hatası:', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Kayıt sırasında bir hata oluştu.');
        }
    }

    public function view($id)
    {
        $offer = Offer::find($id);

        if (!$offer) {
            return redirect()->back()->with('error', 'Teklif bulunamadı.');
        }

        // `created_at` alanını Carbon nesnesine dönüştür
        if (is_string($offer->created_at)) {
            $offer->created_at = Carbon::parse($offer->created_at);
        }

        return view('vendor.voyager.teklif-hazirla.view', compact('offer'));
    }

    public function index(Request $request)
    {
        // Teklif verileri
        $offers = Offer::all();

        foreach ($offers as $offer) {
            Log::info('Teklif Verisi:', $offer->toArray());
        }

        $companies = Firm::with('users')->get();

        return view('vendor.voyager.teklif-hazirla.browse', compact('offers', 'companies'));
    }

    public function update(Request $request, $id)
    {
        try {
            // Doğrulama
            $data = $request->validate([
                'title' => 'required',
                'demand_no' => 'required|string|max:255',
                'delivery_date' => 'required|date',
                'company' => 'required|string|max:255', 
                'person_name' => 'required|string',
                'person_email' => 'required|email',
                'currency' => 'required',
                'description' => 'required|array',
                'description.*' => 'required|string',
                'quantity' => 'required|array',
                'quantity.*' => 'required|numeric|min:1',
                'unit_price' => 'required|array',
                'unit_price.*' => 'required|numeric|min:0',
                'total_price' => 'required|array',
                'total_price.*' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            // Teklif bul ve güncelle
            $offer = Offer::findOrFail($id);

            // Eğer notes boş geliyorsa varsayılan değer ata
             $data['notes'] = $data['notes'] ?? '';

            $offer->update([
                'title' => $data['title'],
                'demand_no' => $data['demand_no'],
                'delivery_date' => $data['delivery_date'],
                'company' => $data['company'],
                'person_name' => $data['person_name'],
                'person_email' => $data['person_email'],
                'currency' => $data['currency'],
                'explanation' => implode(',', $data['description']),
                'piece' => implode(',', $data['quantity']),
                'unit_price' => implode(',', $data['unit_price']),
                'total_price' => implode(',', $data['total_price']),
                'total' => array_sum($data['total_price']),
                'notes' => $data['notes'],
            ]);

            return redirect()->back()->with('success', 'Teklif başarıyla güncellendi.');
        } catch (\Exception $e) {
            Log::error('Teklif Güncelleme Hatası:', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Teklif güncellenirken bir hata oluştu.');
        }
    }

    public function setEditable(Request $request, $id)
    {
        try {
            $offer = Offer::findOrFail($id);
            $offer->is_editable = $request->is_editable;
            $offer->save();

            return response()->json(['success' => true, 'message' => 'Durum başarıyla güncellendi.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function send($id)
{
    $offer = Offer::find($id);

    if (!$offer) {
        return redirect()->back()->with('error', 'Teklif bulunamadı.');
    }

    // Örnek ürünler, veritabanınızdan çekebilirsiniz
    $offer->products = [
        [
            'description' => 'Panel PC',
            'quantity' => 5,
            'unit_price' => '300 €',
            'total_price' => '1.500 €'
        ],
        [
            'description' => 'Test Ürünü',
            'quantity' => 4,
            'unit_price' => '600 €',
            'total_price' => '2.400 €'
        ],
    ];
    $offer->total_price = '3.900 €';

    try {
        Mail::to($offer->person_email)->send(new OfferMail($offer));

        return redirect()->back()->with('success', 'Teklif başarıyla gönderildi.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Teklif gönderimi sırasında bir hata oluştu: ' . $e->getMessage());
    }
}

    public function cancel(Request $request, $id)
    {
        // is_editable = 3 yaparak iptal
        Offer::where('id', $id)->update(['is_editable' => 3]);

        return response()->json([
            'message' => 'Teklif başarıyla iptal edildi.',
        ]);
    }

    public function updateDetails(Request $request, $id)
    {
        $request->validate([
            'details' => 'required|string',
        ]);

        $offer = Offer::findOrFail($id);
        $offer->details = $request->details;
        $offer->save();

        return response()->json(['success' => true, 'message' => 'Detay güncellendi!']);
    }

}