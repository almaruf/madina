<?php

namespace App\Jobs;

use App\Mail\OrderConfirmed;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Load order relationships if not already loaded
        if (!$this->order->relationLoaded('user')) {
            $this->order->load(['user', 'shop', 'items.product', 'address', 'deliverySlot']);
        }

        // Send email to customer
        if ($this->order->user && $this->order->user->email) {
            Mail::to($this->order->user->email)
                ->send(new OrderConfirmed($this->order));
        }
    }
}
