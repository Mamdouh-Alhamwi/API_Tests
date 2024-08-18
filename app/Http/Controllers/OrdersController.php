<?php

namespace App\Http\Controllers;

use App\Models\orders;
use App\Models\products;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;




class OrdersController extends Controller
{
    public function indexOrder(){
        $user = Auth::user();
        $orders = $user->orders;
        return response()->json($orders);
    }

    public function showOrder(orders $order){
        $order->load('products'); // Load the products relationship
        $orderData = [
        'id' => $order->id,
        'user_id' => Auth::id(),
        'user_name' => Auth::user()->name,
        'total_price' => $order->total_price,
        'quantity' => $order->quantity,
        'products' => $order->products->map(function($product) {
            
            return [
                'name' => $product->name,
                'quantity' => $product->pivot->product_quantity,
                'price' => $product->price
            ];
        })->toArray()
    ];
        $order=[$orderData];
        return response()->json($order);
        
    }

    public function storeOrder(Request $request){

    // Validate order data
    $data = $request->validate([
        'products' => ['required', 'array'],
        'products.*.name' => ['required', 'string'],
        //'products.*.quantity' => ['required', 'integer'],
        //'user_id' => ['required', 'integer'],
        //'total' => ['required', 'numeric'],
        //'status' => ['required', 'string'],
        'products.*.liked' => ['nullable','boolean'],
        //'like_product' => ['required', 'boolean'],
        'products.*.quantity' => ['required', 'integer']

    ]);

    // Calculate total
    //$total = 0;
    /*$order = new orders();
    $order->user_id = Auth::id();
    $order->user_name = Auth::user()->name;
    $order->total_price = $total;
    $order->quantity = 1;
    $order->save(); // save the order to get an ID
    */

    $total = 0;
    $quantity = 0;

    foreach ($data['products'] as $product) {
        $quantity += $product['quantity'];
        $total += products::where('name', $product['name'])->first()->price * $product['quantity'];
    }
    
    //$productQuantities = [];

    $order = orders::create([
        'user_id'=> Auth::id(),
        'user_name' => Auth::user()->name,
        'total_price' => $total,
        'quantity' => $quantity,
    ]);
    
    /*
    $productIds = [];
    $productNameQuantity = [];
    foreach ($data['products'] as $product) {
        $productModel = products::where('name', $product['name'])->first();
        if (!$productModel) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        //$total += $productModel->price * $product['quantity'];
        $productIds[] = $productModel->id;
        $productNameQuantity[$productModel->name] = $product['quantity'];
        $liked = isset($product['liked']) && $product['liked'] === true ? true : false;
        $order->products()->sync([
            $productModel->id => ['product_quantity' => $product['quantity'], 'liked' => $liked],
        ]);
    }
    */
    $productsToUpdate = [];
    $productNameQuantity = [];
    foreach ($data['products'] as $product) {
        $productModel = products::where('name', $product['name'])->first();
        if (!$productModel) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        $productNameQuantity[$productModel->name] = $product['quantity'];
        $liked = isset($product['liked']) && $product['liked'] === true ? true : false;
        $productsToUpdate[$productModel->id] = [
            'product_quantity' => $product['quantity'],
            'liked' => $liked,
        ];
    }

    $order->products()->sync($productsToUpdate);
    //$order->update(['total_price' => $total]);

    /*$productIds = [];
    foreach ($data['products'] as $product) {
        $productModel = products::where('name', $product['name'])->first();
        if (!$productModel) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        $total += $productModel->price * $product['quantity'];
        $productIds[] = $productModel->id;
    }*/
    /*foreach ($data['products'] as $product) {
        $productModel = products::where('name', $product['name'])->firstOrFail();
        $total += $productModel->price;
    }*/
    /*foreach($data['products'] as $product) {
        $total += $product->price; 
    }*/

    // Attach products
    /*try {
        $order->products()->attach($productIds);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to attach products', $e], 500);
    }*/
    /*
    try {
        foreach ($productNameQuantity as $productName => $quantity) {
            $productModel = products::where('name', $productName)->first();
            if (!$productModel) {
                return response()->json(['error' => 'Product not found'], 404);
            }
            $order->products()->attach($productModel->id, ['product_quantity' => $quantity]);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to attach products', $e], 500);
    }*/

    /*$products=$request->input('products');
    try {
        foreach($products as $product)
            $order->products()->attach($product['id'],['quantity'=>$product['quantity']]);
        //$order->products()->attach($data['products']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to attach products'], 500);
    }*/

    $order = [
        'order_id' => $order->id,
        'user_name' => Auth::user()->name,
        'total_price' => $total,
        'products' => $productNameQuantity,
    ];
    

    return response()->json(['message: Order created successfully. ',$order], 201); 

    }

    public function updateOrder(Request $request, $id){

    $user = Auth::user();
    $order = $user->orders->where('id', $id)->first();
    //without relations
    //$order = Order::where('user_id', $user->id)->where('id', $id)->first();
    if (!$order) {
        return response()->json(['error' => 'Order not found'], 403);
    }

    $data = $request->validate([
        'products' => ['required', 'array'],
        'products.*.name' => ['required', 'string'],
        'products.*.quantity' => ['required', 'integer'],
        //'status' => ['required', 'string'],
        'products.*.liked' => ['nullable','boolean'],
    ]);

    // Re-calculate total cost based on updated product quantities
    //$total = 0;
    /*foreach ($data['products'] as $product) {
        $productModel = products::where('name', $product['name'])->first();
        if (!$productModel) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        $total += $productModel->price * $product['quantity'];
    }

    // Update order
    try {
        $order->total = $total;
        $order->save();

        // Update product quantities
        foreach ($data['products'] as $product) {
            $productModel = products::where('name', $product['name'])->first();
            $order->products()->updateExistingPivot($productModel->id, ['quantity' => $product['quantity'], 'liked' => $product['liked']]);
        }

        return response()->json(['message' => 'Order updated']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to update order'], 500);
    }
*/

// Re-calculate total cost based on updated product quantities
    /*$total = 0;
    $productsToUpdate = [];
    foreach ($data['products'] as $product) {
    $productModel = products::where('name', $product['name'])->first();
    if (!$productModel) {
        return response()->json(['error' => 'Product not found'], 404);
    }
    $total += $productModel->price * $product['quantity'];
    $productsToUpdate[$productModel->id] = [
        'liked' => $product['liked'],
    ];
}*/

    $total = 0;
    $productsToUpdate = [];
    foreach ($data['products'] as $product) {
        $productModel = products::where('name', $product['name'])->first();
        if (!$productModel) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        $total += $productModel->price * $product['quantity'];
        $productsToUpdate[$productModel->id] = [
            'product_quantity' => $product['quantity'],
            'liked' => $product['liked'],
        ];
    }

    // Update order
    try {
        $order->total_price = $total;
        $order->save();

        // Update product quantities and liked status in pivot table
        $order->products()->sync($productsToUpdate);

        // Update product quantities in orders table
        $newQuantity = 0;
        foreach ($data['products'] as $product) {
            $newQuantity += $product['quantity'];
        }
        $order->quantity = $newQuantity;
        $order->save();

        return response()->json(['message' => 'Order updated']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to update order'], 500);
    }
    /*try {
        $order->total_price = $total;
        $order->save();

        // Update product liked status in pivot table
        $order->products()->sync($productsToUpdate);

        // Update product quantities in orders table
        $newQuantity = 0;
        foreach ($data['products'] as $product) {
            $newQuantity += $product['quantity'];
        }
        $order->quantity = $newQuantity;
        $order->save();

        return response()->json(['message' => 'Order updated']);
    } catch (\Exception $e) {
        //dd($order);
        return response()->json(['error' => 'Failed to update order'], 500);
    }*/
    //return response()->json(['message' => 'Order updated']);
        
    }

    public function deleteOrder(Request $request, $id){

        /*$user = Auth::user();
        $order = $user->orders->where('id', $id)->first();
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        try {
            $order->delete();
            return response()->json(['message' => 'Order deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete order',dd($order)], 500);
        }     */
       
        $user = Auth::user();
        $order = $user->orders->where('id', $id)->first();
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        try {
            $order->products()->detach();
            $order->delete();
            return response()->json(['message' => 'Order deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete order'], 500);
        }
    }

    //--------------------------- Like & Dislike products --------------------------
    public function likeProduct(Request $request, orders $order, products $product)
    {
        // Check if the user has already liked the product
        $pivot = $order->products()->where('products_id', $product->id)->first();

        if ($pivot) {
            // Update the liked status
            $pivot->update(['liked' => true]);
        } else {
            // Create a new pivot entry with liked status
            $order->products()->attach($product->id, ['liked' => true]);
        }

        return response()->json(['message' => 'Product liked successfully']);
    }

    public function dislikeProduct(Request $request, orders $order, products $product)
    {
        // Check if the user has already liked the product
        $pivot = $order->products()->where('products_id', $product->id)->first();

        if ($pivot) {
            // Update the liked status
            $pivot->update(['liked' => false]);
        }

        return response()->json(['message' => 'Product disliked successfully']);
    }

}
