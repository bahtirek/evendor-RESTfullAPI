<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Price_Compare extends Mailable
{
    use Queueable, SerializesModels;

    public $hash;
    public $email;
    public $list;
    public $account;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($hash, $email, $list, $account)
    {
        $this->hash = json_encode(['hash' => $hash, 'email' => $email]);
        $this->email = $email;
        $this->list = $list;
        $this->account = $account;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('priceCheck@FoodConn.com')->view('mails.pricecomparerequest');
    }
}
