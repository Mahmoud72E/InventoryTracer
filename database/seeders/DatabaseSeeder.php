<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Ingredient;
use App\Models\ProductIngredient;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * Ingredients
         */
        $beef = Ingredient::create([
            'name' => 'Beef',
            'stock' => 10000,        // grams
            'initial_stock' => 10000
        ]);

        $cheese = Ingredient::create([
            'name' => 'Cheese',
            'stock' => 5000,
            'initial_stock' => 5000
        ]);

        $onion = Ingredient::create([
            'name' => 'Onion',
            'stock' => 3000,
            'initial_stock' => 3000
        ]);

        /**
         * Product
         */
        $cheeseburger = Product::create([
            'name' => 'Cheeseburger'
        ]);

        /**
         * Product Ingredients (Pivot)
         * Cheeseburger requirements
         */
        ProductIngredient::insert([
            [
                'product_id' => $cheeseburger->id,
                'ingredient_id' => $beef->id,
                'quantity' => 150, // grams
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $cheeseburger->id,
                'ingredient_id' => $cheese->id,
                'quantity' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $cheeseburger->id,
                'ingredient_id' => $onion->id,
                'quantity' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
