<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Device;

class EventController extends VoyagerBaseController
{
    public function create(Request $request)
    {
        $devices = Device::all();
        // Dosyanın vendor/voyager/bildirimler/create.blade.php yolunda olduğundan emin olun
        return view('vendor.voyager.bildirimler.create', compact('devices'));
    }

    public function getTags($deviceId)
    {
        // Cihazı buluyoruz
        $device = \App\Device::find($deviceId);
        
        if (!$device || empty($device->tags)) {
            return response()->json([]);
        }

        // JSON sütununu ({"0":"Quanta...", "200":"..."}) PHP dizisine çevir
        $tagsRaw = json_decode($device->tags, true);
        
        $formattedTags = [];
        if (is_array($tagsRaw)) {
            foreach ($tagsRaw as $key => $value) {
                // Dropdown için id ve name çiftlerini oluşturuyoruz
                $formattedTags[] = [
                    'id'   => $id_key = (string)$key, // "200" gibi anahtarlar
                    'name' => $value                  // "Quanta 5 Hata Günlük" gibi isimler
                ];
            }
        }

        return response()->json($formattedTags);
    }

    public function store(Request $request)
    {
        // Form verilerini doğrula
        $request->validate([
            'items.*.device_id' => 'required',
            'items.*.tag_id'    => 'required',
            'email'             => 'required|email'
        ]);

        try {
            $count = 0;
            // Blade'deki name="items[IDX][...]" yapısından gelen veriyi dönüyoruz
            foreach ($request->items as $item) {
                // Eğer cihaz ve tag seçilmişse kaydet
                if (!empty($item['device_id']) && !empty($item['tag_id'])) {
                    \App\NotifiedEvent::create([
                        'device_id' => $item['device_id'],
                        'tag_id'    => $item['tag_id'],
                        'min_value' => $item['min'] ?? 0,
                        'max_value' => $item['max'] ?? 100,
                        'email'     => $request->email,
                        'status'    => 1
                    ]);
                    $count++;
                }
            }

            return redirect()->back()->with([
                'message'    => "$count adet olay başarıyla kaydedildi.",
                'alert-type' => 'success'
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Olay Kayıt Hatası: " . $e->getMessage());
            return redirect()->back()->with([
                'message'    => "Bir hata oluştu: " . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }

    public function index(Request $request) 
    {
        // Verileri çekiyoruz
        $events = \App\NotifiedEvent::with('device')->orderBy('created_at', 'desc')->get();
        
        // View'a gönderiyoruz
        return view('vendor.voyager.bildirimler.index', compact('events'));
    }

}