<?php

namespace App\Events;

use App\Models\Ingredient;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IngredientStockLow
{
    use Dispatchable, SerializesModels;

    public Ingredient $ingredient;

    /**
     * Create a new event instance.
     */
    public function __construct(Ingredient $ingredient)
    {
        $this->ingredient = $ingredient;
    }
}
