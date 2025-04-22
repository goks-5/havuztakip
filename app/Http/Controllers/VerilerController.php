<?php

namespace App\Http\Controllers;

use App\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VerilerController extends VoyagerBaseController
{
    public function show(Request $request, $id)
{
    $device = Device::findOrFail($id);

    $period = $request->get('period');
    $range = $request->get('range');

    $dateStart = now()->startOfDay();
    $dateEnd = now();

    $datas = collect(); // boş veri koleksiyonu

    if ($range && $period) {
        [$start, $end] = explode(' - ', urldecode($range));
        $dateStart = \Carbon\Carbon::createFromFormat('d.m.Y H:i', $start);
        $dateEnd = \Carbon\Carbon::createFromFormat('d.m.Y H:i', $end);

        $tags = json_decode($device->tags, true) ?? [];

        $typeBases = [
            'endeks' => 0,
            'saatlik' => 100,
            'günlük' => 200,
            'haftalık' => 300,
            'aylık' => 400,
            'yıllık' => 500,
        ];

        $selectedDataIds = [];

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

        $datas = DB::table('device_datas')
            ->where('device_id', $device->id)
            ->whereIn('data_id', $selectedDataIds)
            ->whereBetween('created_at', [$dateStart, $dateEnd])
            ->orderBy('created_at')
            ->get();
    } else {
        $tags = json_decode($device->tags, true) ?? [];
        $selectedDataIds = [];
    }

    return view('vendor.voyager.veriler.show', [
        'device' => $device,
        'datas' => $datas,
        'tags' => $tags ?? [],
        'selectedDataIds' => $selectedDataIds ?? [],
    ]);
}

}
