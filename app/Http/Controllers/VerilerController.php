<?php

namespace App\Http\Controllers;

use App\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VerilerController extends VoyagerBaseController
{
    public function index(Request $request)
    {
        // Gelen search parametresi
        $search = $request->input('search');

        // Silinmemiş ve kullanıcıya ait cihazları temel sorgu
        $query = DB::table('devices')
                   ->whereNull('deleted_at')
                   ->where('company_id', Auth::user()->company_id);

        // Eğer arama terimi geldiyse ilgili sütunlarda filtre uygula
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('device_id', 'like', "%{$search}%")
                  ->orWhere('name',      'like', "%{$search}%")
                  ->orWhere('tags',      'like', "%{$search}%");
            });
        }

        // Sayfala, arama parametresini URL’de tut
        $devices = $query->paginate(10)
                         ->appends(['search' => $search]);

        // View’a cihazları ve arama terimini yolla
        return view('vendor.voyager.veriler.browse', compact('devices', 'search'));
    }

    public function show(Request $request, $id)
    {
        // Sadece kullanıcıya ait cihazı getir
        $device = Device::where('company_id', Auth::user()->company_id)
                        ->findOrFail($id);

        $period = $request->get('period');
        $range  = $request->get('range');

        $dateStart = now()->startOfDay();
        $dateEnd   = now();

        $datas = collect(); // boş veri koleksiyonu
        $tags  = json_decode($device->tags, true) ?? [];
        $selectedDataIds = [];

        if ($range && $period) {
            [$start, $end] = explode(' - ', urldecode($range));
            $dateStart = Carbon::createFromFormat('d.m.Y H:i', $start);
            $dateEnd   = Carbon::createFromFormat('d.m.Y H:i', $end);

            $typeBases = [
                'endeks'  => 0,
                'saatlik' => 100,
                'günlük'  => 200,
                'haftalık'=> 300,
                'aylık'   => 400,
                'yıllık'  => 500,
            ];

            // Seçilen data_id’leri belirle
            if ($period === 'tümü') {
                foreach (range(0, 5) as $i) {
                    $base = $i * 100;
                    foreach ($tags as $dataId => $label) {
                        if ((int)$dataId >= $base && (int)$dataId < $base + 100) {
                            $selectedDataIds[] = (int)$dataId;
                        }
                    }
                }
            } else {
                $base = $typeBases[$period] ?? 0;
                foreach ($tags as $dataId => $label) {
                    if ((int)$dataId >= $base && (int)$dataId < $base + 100) {
                        $selectedDataIds[] = (int)$dataId;
                    }
                }
            }

            // Cihaz verilerini çek
            $datas = DB::table('device_datas')
                ->where('device_id', $device->id)
                ->whereIn('data_id', $selectedDataIds)
                ->whereBetween('created_at', [$dateStart, $dateEnd])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('vendor.voyager.veriler.show', [
            'device'            => $device,
            'datas'             => $datas,
            'tags'              => $tags,
            'selectedDataIds'   => $selectedDataIds,
        ]);
    }
}
