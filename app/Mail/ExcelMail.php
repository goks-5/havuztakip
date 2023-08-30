<? 
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExcelMail extends Mailable
{
    use Queueable, SerializesModels;

    public $excelPath;
    public $excelTitle;
    public $emailContent;

    public function __construct($excelPath,$excelTitle, $emailContent)
    {
        $this->excelPath = $excelPath;
        $this->excelTitle = $excelTitle;
        $this->emailContent = $emailContent; 
    }

    public function build()
    {
        return $this->subject($this->excelTitle)
                    ->attach($this->excelPath)
                    ->view('emails.excel_mail');
    }
}