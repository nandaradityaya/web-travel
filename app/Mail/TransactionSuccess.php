<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionSuccess extends Mailable
{
    use Queueable, SerializesModels;

    public $data; //untuk menampung data

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data; //untuk memproses data yang masuk ke transactionsucces
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
        ->from('hi@nandul.com', 'NOMADS') //sumber utama email yang akan di kirim
        ->subject('Tiket NOMADS Anda')
        ->view('email.transaction-success');
    }
}
