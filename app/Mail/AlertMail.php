<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $eventData;

    public function __construct($eventData)
    {
        $this->eventData = $eventData;
    }

    public function build()
    {
        return $this->subject('Enerji Yönetimi - Limit Aşım Uyarısı!')
                    ->view('emails.alert');
    }
}