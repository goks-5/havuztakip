<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckDeviceLimits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:device-limits'; // <--- Burayı 'command:name' yerine bu şekilde değiştir

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cihazların min/max limitlerini kontrol eder ve mail atar.'; // <--- Açıklama eklemek iyidir
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
{
    // Aktif kuralları 100'erli gruplar halinde çek (Hafıza dostu)
    \App\NotifiedEvent::where('status', 1)
        ->chunk(100, function ($rules) {
            foreach ($rules as $rule) {
                $device = \App\Device::find($rule->device_id);
                if (!$device || empty($device->last_data)) continue;

                $lastData = json_decode($device->last_data, true);
                $currentValue = isset($lastData[$rule->tag_id]) ? floatval($lastData[$rule->tag_id]) : null;

                if ($currentValue !== null) {
                    // Eşik kontrolü
                    if ($currentValue < $rule->min_value || $currentValue > $rule->max_value) {
                        
                        // ÖNEMLİ: Eğer son 1 saat içinde zaten mail atılmışsa PAS GEÇ
                        if ($rule->last_notified_at && \Carbon\Carbon::parse($rule->last_notified_at)->diffInHours() < 1) {
                            continue; 
                        }

                        // Mail Gönderimi
                        $this->sendAlert($rule, $device, $currentValue);

                        // Son bildirim zamanını güncelle
                        $rule->last_notified_at = now();
                        $rule->save();
                    }
                }
            }
        });
}

private function sendAlert($rule, $device, $currentValue)
{
    $tags = json_decode($device->tags, true);
    $tagName = $tags[$rule->tag_id] ?? $rule->tag_id;

    $data = [
        'device_name'   => $device->name,
        'tag_name'      => $tagName,
        'current_value' => $currentValue,
        'min'           => $rule->min_value,
        'max'           => $rule->max_value
    ];

    try {
        \Illuminate\Support\Facades\Mail::to($rule->email)->send(new \App\Mail\AlertMail($data));
    } catch (\Exception $e) {
        \Log::error("Mail gönderim hatası: " . $e->getMessage());
    }
}

}
