<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendInfoCircleMail extends Command
{
    // Kullanım:
    // php artisan mail:info-circle --to="a@b.com,c@d.com" [--company_id=5]
    protected $signature = 'mail:info-circle {--to=} {--company_id=}';
    protected $description = 'Info-circle özet e-postasını gönderir';

    public function handle()
    {
        $to = $this->option('to');
        if (!$to) {
            $this->error('--to parametresi gerekli. Örn: --to="gookceturun@gmail.com"');
            return 1;
        }
        $recipients = array_map('trim', explode(',', $to));
        $companyId  = $this->option('company_id');

        // 1) Cihazları çek (company_id verilmişse filtrele)
        $q = DB::table('devices')->whereNull('deleted_at');
        if (!is_null($companyId)) {
            $q->where('company_id', $companyId);
        }
        $devices = $q->get([
            'id','company_id','mac','name','tags','last_data','last_at','tags_last_change'
        ]);

        // 2) Zaman aşımı ayarları
        $timeout1 = (int) (setting('device.ofline') * 60);
        $timeout2 = (int) setting('device.oflinesayac');
        $timeout3 = (int) (setting('device.tag') * 60);

        // 3) Özet hesapla
        $summary = [
            'ofline'          => 0,
            'deviceCount'     => $devices->count(),
            'pointCount'      => 0,
            'oflineCount'     => 0,
            'oflineDevices'   => [],
            'changeTagsCount' => 0,
            'changeTags'      => [],
        ];

        foreach ($devices as $device) {
            // <1000 olanlar gerçek tag isimleri
            $tags = [];
            if (!is_null($device->tags)) {
                $tags = array_filter(json_decode($device->tags, true), function ($k) {
                    return $k < '1000';
                }, ARRAY_FILTER_USE_KEY);
            }

            $summary['pointCount'] += count($tags);

            $timeout = ($device->mac === '00:00:00:00:00:01') ? $timeout2 : $timeout1;

            if (strtotime($device->last_at) + $timeout < time() && $device->mac !== '00:00:00:00:00:02') {
                $summary['oflineCount']++;
                $summary['oflineDevices'][] = [
                    'name'    => $device->name,
                    'mac'     => $device->mac,
                    'last_at' => $device->last_at,
                ];
                $summary['ofline'] = 1;
            }

            // 1000+ anahtarlar: değişim izleme
            $changeTags = [];
            if (!is_null($device->tags)) {
                $changeTags = array_filter(json_decode($device->tags, true), function ($k) {
                    return $k >= '1000';
                }, ARRAY_FILTER_USE_KEY);
            }
            $changeAt = json_decode($device->tags_last_change, true) ?: [];
            $changeTags = array_replace($changeTags, $changeAt);

            foreach ($changeTags as $tagkey => $value) {
                if (strtotime($value) + $timeout3 < time()) {
                    $tagName = $tags[$tagkey - 1000] ?? ('Tag#' . ($tagkey - 1000));
                    $summary['changeTags'][] = [
                        'name'        => $device->name,
                        'tag'         => $tagName,
                        'last_change' => $value,
                    ];
                    $summary['changeTagsCount']++;
                    $summary['ofline'] = 1;
                }
            }
        }

        // 4) Firma adı (companies.name)
        $companyName = config('app.name', 'Enerji Yönetim');
        if (!is_null($companyId)) {
            $companyName = DB::table('companies')->where('id', $companyId)->value('name') ?: $companyName;
        } else {
            $ids = $devices->pluck('company_id')->filter()->unique();
            if ($ids->count() === 1) {
                $companyName = DB::table('companies')->where('id', $ids->first())->value('name') ?: $companyName;
            } elseif ($ids->count() > 1) {
                $companyName = 'Tüm Firmalar';
            }
        }

        // 5) Konu & gönderim
        $subject = sprintf(
            '%s - Enerji Yönetim - Durum Özeti | Cihaz:%d Nokta:%d Offline:%d',
            $companyName,
            $summary['deviceCount'],
            $summary['pointCount'],
            $summary['oflineCount']
        );

        // HTML view varsa onu kullanıyoruz (resources/views/emails/info_circle.blade.php)
        Mail::send('emails.info_circle', ['summary' => $summary, 'companyName' => $companyName], function ($m) use ($recipients, $subject) {
            $m->to($recipients)->subject($subject);
        });

        $this->info('Gönderildi: ' . implode(', ', $recipients));
        $this->info('Konu: ' . $subject);
        return 0;
    }
}
