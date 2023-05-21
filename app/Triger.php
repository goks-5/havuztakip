<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Triger extends Model
{


    public static function check()
    {

        $all = Triger::get();

        foreach ($all as $triger) {

            $deviceTag = explode('_', $triger->device_tags);
            $device = Device::where('id', $deviceTag[0])->first();
            $lastdata = json_decode($device->last_data, true);
            $tags = json_decode($device->tags, true);
            $tag = $tags[$deviceTag[1]];
            $data = (float)$lastdata[$deviceTag[1]];
            $alarm = false;
            if ($data >= (float)$triger->level && $triger->condition == '>=') {
                $alarm = true;
            }
            if ($data <= (float)$triger->level &&  $triger->condition == '<=') {
                $alarm = true;
            }
            if ($data == (float)$triger->level &&  $triger->condition == '==') {
                $alarm = true;
            }

            if ($triger->condition == '!=') {
                if ($data != (float)$triger->level ) {
                    $alarm = true;
                    $triger->level = $data;
                }
                $triger->last_status = 0;
            }

            if ($triger->last_status == 0 && $alarm) {
                self::sendTriger($triger, $device->name . ' ' . $tag . ' ' . $triger->condition . ' ' . $triger->level . ' ( ' . $data . ' )');
                $triger->last_status = 1;
            } elseif (!$alarm && $triger->last_status <> 0) {
                self::sendTriger($triger, 'Alarm End' . $device->name . ' ' . $tag . ' !' . $triger->condition . ' ' . $triger->level . ' ( ' . $data . ' )');
                $triger->last_status = 0;
            }
            $triger->save();
        }
    }

    public static function sendTriger($triger, $subject)
    {
        $emails = json_decode($triger->users);
        $html = $triger->mail_body;
        foreach ($emails as $email) {
            Mail::send([],[], function ($msg) use ($html,$email, $subject) {
                $msg->to($email)->subject($subject)->setBody($html, 'text/html');;
            });
        }
    }
}
