<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class FoodConn_Order extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $account;
    public $dt;
    public $vendorName;
    public $vendorNote;
    public $orderId;
    public $update;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $vendorNote, $vendorName, $account, $orderId, $update)
    {
        $this->order = $order;
        $this->account = $account;
        $this->dt = new Carbon(); 
        $this->vendorName = $vendorName;;
        $this->vendorNote = $vendorNote;
        $this->orderId = $orderId;
        $this->update = $update;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.order');
    }
}
