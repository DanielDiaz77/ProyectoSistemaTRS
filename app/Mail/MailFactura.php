<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailFactura extends Mailable
{
    use Queueable, SerializesModels;

    public $path;
    public $data;
    public $num_pre;
    public $emit;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($path,$data,$num_pre,$emit)
    {
        $this->path = $path;
        $this->data = $data;
        $this->num_pre = $num_pre;
        $this->emit = $emit;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){

        return $this->from($this->emit)
        ->view('mail.facturaMail')
        ->attach($this->path, [
            'as' => 'Factura-'.$this->num_pre.'.pdf',
            'mime' => 'application/pdf'
        ]);
    }
}
