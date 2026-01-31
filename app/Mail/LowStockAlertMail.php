<?php

namespace App\Mail;

use App\Models\Ingredient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public Ingredient $ingredient;

    /**
     * Create a new message instance.
     */
    public function __construct(Ingredient $ingredient)
    {
        $this->ingredient = $ingredient;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->subject('⚠️ Low Stock Alert: ' . $this->ingredient->name)
            ->view('emails.low_stock_alert')
            ->with([
                'ingredientName' => $this->ingredient->name,
                'currentStock'   => $this->ingredient->stock,
                'initialStock'   => $this->ingredient->initial_stock,
            ]);
    }
}
