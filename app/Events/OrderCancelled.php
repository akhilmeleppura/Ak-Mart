<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCancelled
{
    use Dispatchable, SerializesModels;

    public Order $order;
    public string $reason;

    public function __construct(Order $order, string $reason = '')
    {
        $this->order = $order;
        $this->reason = $reason;
    }
}
