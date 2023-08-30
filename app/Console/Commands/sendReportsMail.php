<?php

namespace App\Console\Commands;

use App\CompanySetting;
use App\Device;
use App\Exports\ReportExport;
use App\Mail\ExcelMail;
use App\Report;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class sendReportsMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Mail Reports ';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $reports = Report::whereNotNull('mail_to')->get();
        foreach ($reports as $report) {
            $individualEmails = explode(',', $report->mail_to);
            $validEmails = [];
            foreach ($individualEmails as $individualEmail) {
                $trimmedEmail = trim($individualEmail);
                if (!empty($trimmedEmail) && filter_var($trimmedEmail, FILTER_VALIDATE_EMAIL)) {
                    $validEmails[] = $trimmedEmail;
                }
            }
            dump(count($validEmails));
            try {
                if ($this->checkIsSend($report) && !empty($validEmails)) {
                    $excelData = $this->excelData($report);

            dump(count($excelData));
                    if(!empty($excelData)){
                        $report->report_send_date = $this->reportDate($report);

            dump(count($report->report_send_date));
                        $report->save();
                        $this->sendMail($validEmails,$excelData, "Enerji Yönetim Rapor : " . $report->name);
                    }
                }
            } catch (\Throwable $th) {
                dump($th);
            }
            
        }
    }

    protected function checkIsSend($report):Bool {
        $dateStart = Carbon::parse($this->reportDate($report)); 
        $now = Carbon::now()->subHour();
        $sendDate = Carbon::parse($report->report_send_date);
        
        return $now->gt($dateStart)  && $dateStart->gt($sendDate) ;

    }

    protected function reportDate($report){
        $date = date('Y-m-d H:i');
        $setting = CompanySetting::select('day_start_hour', 'week_start_day', 'month_start_day')->find($report->company_id);
        switch ($report->period) {
            case 1:
                $dateStart = Carbon::parse(strtotime($date))
                    ->startOfDay()->addHours($setting['day_start_hour'])->toDateTimeString();
                break;
            case 2:
                $dateStart = Carbon::parse(strtotime($date))
                    ->startOfDay()->startOfWeek($setting['week_start_day'])->addHours($setting['day_start_hour'])->toDateTimeString();
                break;
            case 3:
                $dateStart = Carbon::parse(strtotime($date))
                    ->startOfDay()->startOfMonth()->addDays($setting['month_start_day'] - 1)->addHours($setting['day_start_hour'])->toDateTimeString();

                break;
            default:
                $dateStart = Carbon::parse(strtotime($date))
                    ->startOfDay()->startOfYear()->addDays($setting['month_start_day'] - 1)->addHours($setting['day_start_hour'])->toDateTimeString();
             break;
        }
        return $dateStart;

    }


    public function sendMail($mails,$data,$title){
        $path = 'exports/' . Str::slug($title . date('_Y-m-d H:i')) . '.xlsx';
        Excel::store(new ReportExport(array_values($data)), $path);
        $emailContent = "Enerji Yönetim den otomatik oluşturulan raporu ekden indirebilirsiniz.";
        foreach($mails as $mail){
            Mail::to($mail)->send(new ExcelMail(storage_path('app/'.$path),$title, $emailContent));
        }

    }

    public function excelData($report): array
    {
        $date = date('Y-m-d H:i');
        $lenght = $report->lenght;
        $type = $report->type;
        $tags = json_decode($report->tags, true);
        $titles = json_decode($report->titles, true);
        

        $setting = CompanySetting::select('day_start_hour', 'week_start_day', 'month_start_day')->find($report->company_id);
        switch ($report->period) {
            case 1:
                $dateStart = Carbon::parse(strtotime($date . " -$report->lenght day"))
                    ->startOfDay()->addHours($setting['day_start_hour'])->toDateTimeString();
                $dataDiff = 200;
                $dateparam = "day";
                break;
            case 2:
                $dateStart = Carbon::parse(strtotime($date . " -$report->lenght week"))
                    ->startOfDay()->startOfWeek($setting['week_start_day'])->addHours($setting['day_start_hour'])->toDateTimeString();
                $dataDiff = 300;
                $dateparam = "week";
                break;
            case 3:
                $dateStart = Carbon::parse(strtotime($date . " -$report->lenght month"))
                    ->startOfDay()->startOfMonth()->addDays($setting['month_start_day'] - 1)->addHours($setting['day_start_hour'])->toDateTimeString();
                $dataDiff = 400;
                $dateparam = "month";
                break;
            default:
                $dateStart = Carbon::parse(strtotime($date . " -$report->lenght year"))
                    ->startOfDay()->startOfYear()->addDays($setting['month_start_day'] - 1)->addHours($setting['day_start_hour'])->toDateTimeString();
                $dataDiff = 500;
                $dateparam = "year";
                break;
        }


        $gunler = array(
            'Pazartesi',
            'Salı',
            'Çarşamba',
            'Perşembe',
            'Cuma',
            'Cumartesi',
            'Pazar'
        );

        $aylar = array(
            'Ocak',
            'Şubat',
            'Mart',
            'Nisan',
            'Mayıs',
            'Haziran',
            'Temmuz',
            'Ağustos',
            'Eylül',
            'Ekim',
            'Kasım',
            'Aralık'
        );


        $data = [];
        if ($type == 1 || $type == 3) {
            $index = 0;
            foreach ($tags as $key => $tag) {
                $device = explode('_', $tag);
                if ($type == 3) {
                $data[$index]['Sayaç'] = rtrim(rtrim(rtrim($titles[$key], 'Günlük'), 'Haftalık'), 'Aylık') . 'Endeks';
                $data[$index + 1]['Sayaç'] = $titles[$key];
                }else{
                    $data[$index]['Sayaç'] = $titles[$key];
                }
                $veriler = DB::table('device_datas')
                    ->select('created_at', 'value')
                    ->where('device_id', $device[0])
                    ->where('data_id', $device[1])
                    ->where('created_at', '<', $date)
                    ->where('created_at', '>=', $dateStart)
                    ->limit($lenght)->orderBy('created_at', $report->order_direction)->get();
                $veriArray = [];
                foreach ($veriler as $veri) {
                    $ay = $aylar[date('m', strtotime($veri->created_at)) - 1];
                    $gun = $gunler[date('N', strtotime($veri->created_at)) - 1];
                    $veriArray[date('d', strtotime($veri->created_at)) . " " . $ay] = $veri->value;
                }
                if ($report->order_direction == 'desc') {
                    for ($addDate = $lenght; $addDate >= 0; $addDate--) {
                        $onDate = date('Y-m-d H:i', strtotime($dateStart . " +$addDate $dateparam"));
                        $ay = $aylar[date('m', strtotime($onDate)) - 1];
                        $gun = $gunler[date('N', strtotime($onDate)) - 1];
                        if ($type == 3) {
                            $data[$index][date('d', strtotime($onDate)) . " " . $ay] = Device::getDayFirstValueOnCache($device[0],  $device[1] - $dataDiff, $onDate);
                            $data[$index + 1][date('d', strtotime($onDate)) . " " . $ay] =  $veriArray[date('d', strtotime($onDate)) . " " . $ay] ?? '-';
                        }else{
                            $data[$index][date('d', strtotime($onDate)) . " " . $ay] =  $veriArray[date('d', strtotime($onDate)) . " " . $ay] ?? '-';
                            
                        }
                        }
                } else {
                    for ($addDate = 0; $addDate <= $lenght; $addDate++) {
                        $onDate = date('Y-m-d H:i', strtotime($dateStart . " +$addDate $dateparam"));
                        $ay = $aylar[date('m', strtotime($onDate)) - 1];
                        $gun = $gunler[date('N', strtotime($onDate)) - 1];
                        if ($type == 3) {
                            $data[$index][date('d', strtotime($onDate)) . " " . $ay] = Device::getDayFirstValueOnCache($device[0],  $device[1] - $dataDiff, $onDate);
                            $data[$index + 1][date('d', strtotime($onDate)) . " " . $ay] =  $veriArray[date('d', strtotime($onDate)) . " " . $ay] ?? '-';
                        }else{
                            $data[$index][date('d', strtotime($onDate)) . " " . $ay] =  $veriArray[date('d', strtotime($onDate)) . " " . $ay] ?? '-';
                            
                        }
                        }
                }

                ++$index;
                if ($type == 3) {
                    ++$index;
                }
            }
        } else {


            foreach ($tags as $key => $tag) {
                $device = explode('_', $tag);

                $veriler = DB::table('device_datas')
                    ->select('created_at', 'value')
                    ->where('device_id', $device[0])
                    ->where('data_id', $device[1])
                    ->where('created_at', '<', $date)
                    ->where('created_at', '>=', $dateStart)
                    ->limit($lenght)->orderBy('created_at', $report->order_direction)->get();
                $veriArray = [];
                foreach ($veriler as $veri) {
                    $ay = $aylar[date('m', strtotime($veri->created_at)) - 1];
                    $gun = $gunler[date('N', strtotime($veri->created_at)) - 1];
                    $tarih = date('d', strtotime($veri->created_at)) . " " . $ay . " " . date('Y', strtotime($veri->created_at)) . " " . $gun;
                    $veriArray[$tarih] = $veri->value;
                }
                if ($report->order_direction == 'desc') {
                    for ($addDate = $lenght; $addDate >= 0; $addDate--) {
                        $onDate = date('Y-m-d H:i', strtotime($dateStart . " +$addDate $dateparam"));
                        $ay = $aylar[date('m', strtotime($onDate)) - 1];
                        $gun = $gunler[date('N', strtotime($onDate)) - 1];
                        $tarih = date('d', strtotime($onDate)) . " " . $ay . " " . date('Y', strtotime($onDate)) . " " . $gun;
                        $data[$tarih]['Tarih']  = $tarih;
                        if ($type == 4) {
                            $data[$tarih][rtrim(rtrim(rtrim($titles[$key], 'Günlük'), 'Haftalık'), 'Aylık') . 'Endeks'] = Device::getDayFirstValueOnCache($device[0],  $device[1] - $dataDiff, $onDate);
                        }
                        $data[$tarih][$titles[$key]] =  $veriArray[$tarih] ?? '-';
                    }
                } else {
                    for ($addDate = 0; $addDate <= $lenght; $addDate++) {
                        $onDate = date('Y-m-d H:i', strtotime($dateStart . " +$addDate $dateparam"));
                        $ay = $aylar[date('m', strtotime($onDate)) - 1];
                        $gun = $gunler[date('N', strtotime($onDate)) - 1];
                        $tarih = date('d', strtotime($onDate)) . " " . $ay . " " . date('Y', strtotime($onDate)) . " " . $gun;
                        $data[$tarih]['Tarih']  = $tarih;
                        if ($type == 4) {
                            $data[$tarih][rtrim(rtrim(rtrim($titles[$key], 'Günlük'), 'Haftalık'), 'Aylık') . 'Endeks'] = Device::getDayFirstValueOnCache($device[0],  $device[1] - $dataDiff, $onDate);
                        }
                        $data[$tarih][$titles[$key]] =  $veriArray[$tarih] ?? '-';
                    }
                }
            }
        }

        return $data;
    }
}
