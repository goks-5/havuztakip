<?php 
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExcelMail extends Mailable
{
    use Queueable, SerializesModels;

    public $excelPath;
    public $emailTitle;
    public $emailContent;

    public function __construct($excelPath,$emailTitle, $emailContent)
    {
        $this->excelPath = $excelPath;
        $this->emailTitle = $emailTitle;
        $this->emailContent = $emailContent; 
    }

    public function build()
    {
        return $this->subject($this->emailTitle)
                    ->attach($this->excelPath)
                    ->view('emails.excel_mail');
    }
}