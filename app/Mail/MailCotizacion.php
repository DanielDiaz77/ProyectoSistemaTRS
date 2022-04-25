<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailCotizacion extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf;
    public $data;
    public $num_cot;
    public $emit;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdf,$data,$num_cot,$emit)
    {
        $this->pdf = $pdf;
        $this->data = $data;
        $this->num_cot = $num_cot;
        $this->emit = $emit;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->emit)
        ->view('mail.cotizacionMail')
        ->attachData($this->pdf, $this->num_cot.'.pdf', [
            'mime' => 'application/pdf',
        ]);
    }
}
