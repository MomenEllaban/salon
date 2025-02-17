<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class MailMailableSend extends Mailable
{
    use Queueable, SerializesModels;

    public $mailable;

    public $data;

    public $templateData;

    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct($mailable, $data, $type = '')
    {
        $this->mailable = $mailable ?? '';
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    // public function build()
    // {
    //     $this->templateData = $this->mailable->defaultNotificationTemplateMap->template_detail;

    //     foreach ($this->data as $key => $value) {
    //         $this->templateData = str_replace('[[ '.$key.' ]]', $this->data[$key], $this->templateData);
    //     }

    //     $message = $this->markdown('mail.markdown');

    //     return $message; //Send mail
    // }

    public function content()
    {
        $this->templateData = $this->mailable->defaultNotificationTemplateMap->template_detail;

        foreach ($this->data as $key => $value) {
          //  if(is_string($this->data[$key])) {
                $this->templateData = str_replace('[[ '.$key.' ]]', $this->data[$key], $this->templateData);
          //  }
        }

        return new Content(
            markdown: 'mail.markdown',
        );
    }

    public function attachments()
    {
        $files = [];
        if($this->type == 'complete_booking') {
            \Log::info($this->data);
            $pdf = Pdf::loadHTML(view("mail.invoice-templates.".setting('template'), ['data' => $this->data])->render());
            $files[0] = Attachment::fromData(function() use($pdf) {
                return $pdf->output();
            }, 'Invoice.pdf')
                ->withMime('application/pdf');
        }

        return $files;
    }
}
