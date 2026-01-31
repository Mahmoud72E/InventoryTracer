<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ingredient;
use App\Events\IngredientStockLow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * POST /api/orders
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($request) {
            
            // Collect total required ingredients
            $requiredIngredients = [];

            foreach ($request->items as $item) {
                $product = Product::with('ingredients')->find($item['product_id']);

                foreach ($product->ingredients as $ingredient) {
                    $neededQty = $ingredient->pivot->quantity * $item['quantity'];

                    if (!isset($requiredIngredients[$ingredient->id])) {
                        $requiredIngredients[$ingredient->id] = [
                            'ingredient' => $ingredient,
                            'required' => 0
                        ];
                    }

                    $requiredIngredients[$ingredient->id]['required'] += $neededQty;
                }
            }

          
            // Check stock availability
        
            foreach ($requiredIngredients as $data) {
                $ingredient = $data['ingredient'];
                $required   = $data['required'];

                if ($ingredient->stock < $required) {
                    return response()->json([
                        'message' => 'Insufficient stock',
                        'ingredient' => $ingredient->name,
                        'required' => $required,
                        'available' => $ingredient->stock,
                    ], 400);
                }
            }

            
            // Deduct stock
            foreach ($requiredIngredients as $data) {
                $ingredient = $data['ingredient'];
                $ingredient->decrement('stock', $data['required']);
            }

             // Create Order
        
            $order = Order::create([
                'status' => 'completed'
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            // Dispatch low stock events (after deduction)
           
            foreach ($requiredIngredients as $data) {
                $ingredient = Ingredient::find($data['ingredient']->id);

                if ($ingredient->stock < ($ingredient->initial_stock * 0.5)) {
                    event(new IngredientStockLow($ingredient));
                }
            }

            return response()->json([
                'message' => 'Order placed successfully',
                'order_id' => $order->id
            ], 200);
        });
    }
}
