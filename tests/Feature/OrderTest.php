<?php

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Run the DatabaseSeeder
    $this->seed();
});

it('places an order successfully when ingredients are in stock', function () {

    $product = Product::where('name', 'Cheeseburger')->firstOrFail();

    $response = $this->postJson('/api/orders', [
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => 2,
            ],
        ],
    ]);

    $response
        ->assertStatus(200)
        ->assertJson([
            'message' => 'Order placed successfully',
        ]);

    // Assert order created
    $this->assertDatabaseCount('orders', 1);
    $this->assertDatabaseCount('order_items', 1);

    // Assert ingredients stock deducted correctly
    $beef = Ingredient::where('name', 'Beef')->first();
    $cheese = Ingredient::where('name', 'Cheese')->first();
    $onion = Ingredient::where('name', 'Onion')->first();

    expect($beef->stock)->toBe(10000 - (150 * 2));
    expect($cheese->stock)->toBe(5000 - (30 * 2));
    expect($onion->stock)->toBe(3000 - (20 * 2));
});

it('fails the entire order when an ingredient is out of stock', function () {

    // Force Beef stock to be insufficient
    Ingredient::where('name', 'Beef')->update([
        'stock' => 100, // less than required
    ]);

    $product = Product::where('name', 'Cheeseburger')->firstOrFail();

    $response = $this->postJson('/api/orders', [
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => 1, // needs 150g beef
            ],
        ],
    ]);

    $response
        ->assertStatus(400)
        ->assertJson([
            'message' => 'Insufficient stock',
            'ingredient' => 'Beef',
        ]);

    // Assert NO order created
    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('order_items', 0);

    // Assert stock NOT deducted (transaction rolled back)
    $beef = Ingredient::where('name', 'Beef')->first();
    expect($beef->stock)->toBe(100);
});
