<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCertificate extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;
    public $pdf;
    public function __construct($data,$pdf)
    {
        $this->data = $data;
//        error_log($this->data);
//        dd($this->data);

        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.Certificate')
            ->with('img_url',$this->data['img_url'])
            ->with('body',$this->data['body'])
            ->with('qr_img_url',$this->data['qr_img_url'])
            ->subject('CELT Exam Certificate')
            ->attachData($this->pdf, "text.pdf");

    }
}
