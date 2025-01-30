<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OfferMail extends Mailable
{
    use Queueable, SerializesModels;

    public $offer;

    public function __construct($offer)
    {
        $this->offer = $offer;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Yeni Teklif: ' . $this->offer->title)
                    ->view('emails.offer')
                    ->with([
                        'offer' => $this->offer,
                    ]);
    }
}
