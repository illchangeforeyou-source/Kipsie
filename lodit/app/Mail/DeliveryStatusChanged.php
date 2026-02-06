<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeliveryStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $status;
    public $notes;

    public function __construct($order, $status, $notes = null)
    {
        $this->order = $order;
        $this->status = $status;
        $this->notes = $notes;
    }

    public function build()
    {
        $subject = $this->status === 'delivered' ? 'Your order has been delivered' : 'Your order has been cancelled';

        return $this->subject($subject)
                    ->view('emails.delivery-status-changed')
                    ->with([
                        'order' => $this->order,
                        'status' => $this->status,
                        'notes' => $this->notes,
                    ]);
    }
}
