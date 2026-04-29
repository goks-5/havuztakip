<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Device;
use App\NotifiedEvent;
use Illuminate\Support\Facades\Log;

class EventController extends VoyagerBaseController
{
    public function index(Request $request) 
    {
        $events = NotifiedEvent::with('device')->orderBy('created_at', 'desc')->get();
        return view('vendor.voyager.bildirimler.index', compact('events'));
    }

    public function create(Request $request)
    {
        $devices = Device::all();
        return view('vendor.voyager.bildirimler.create', compact('devices'));
    }

    public function getTags($deviceId)
    {
        $device = Device::find($deviceId);
        if (!$device || empty($device->tags)) return response()->json([]);

        $tagsRaw = json_decode($device->tags, true);
        $formattedTags = [];
        if (is_array($tagsRaw)) {
            foreach ($tagsRaw as $key => $value) {
                $formattedTags[] = ['id' => (string)$key, 'name' => $value];
            }
        }
        return response()->json($formattedTags);
    }

    public function store(Request $request)
    {
        $request->validate(['items.*.device_id' => 'required', 'items.*.tag_id' => 'required', 'items.*.email' => 'required|email']);

        try {
            foreach ($request->items as $item) {
                NotifiedEvent::create([
                    'device_id' => $item['device_id'],
                    'tag_id'    => $item['tag_id'],
                    'min_value' => $item['min'] ?? 0,
                    'max_value' => $item['max'] ?? 100,
                    'email'     => $item['email'],
                    'status'    => 1
                ]);
            }
            return redirect()->route('events.index')->with(['message' => "Kaydedildi.", 'alert-type' => 'success']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage(), 'alert-type' => 'error']);
        }
    }

    // --- DÜZENLEME VE SİLME METODLARI ---

    // edit metodunu bu şekilde değiştir
    public function edit(Request $request, $id)
    {
        $event = NotifiedEvent::findOrFail($id);
        $devices = Device::all();
        return view('vendor.voyager.bildirimler.edit', compact('event', 'devices'));
    }

    // destroy metodunu bu şekilde değiştir
    public function destroy(Request $request, $id)
    {
        $event = NotifiedEvent::findOrFail($id);
        $event->delete();
        return redirect()->route('events.index')->with([
            'message'    => 'Kural silindi.',
            'alert-type' => 'success'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['device_id' => 'required', 'tag_id' => 'required', 'email' => 'required|email']);
        
        $event = NotifiedEvent::findOrFail($id);
        $event->update($request->all());

        return redirect()->route('events.index')->with(['message' => 'Kural başarıyla güncellendi.', 'alert-type' => 'success']);
    }

}