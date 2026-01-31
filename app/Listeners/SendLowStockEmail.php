<?php

namespace App\Listeners;

use App\Events\IngredientStockLow;
use App\Mail\LowStockAlertMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendLowStockEmail implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(IngredientStockLow $event): void
    {
        $ingredient = $event->ingredient;

        // You can move this to config later
        $managerEmail = config('mail.manager_email', 'manager@example.com');

        Mail::to($managerEmail)->send(
            new LowStockAlertMail($ingredient)
        );
    }
}
