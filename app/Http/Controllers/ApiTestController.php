<?php

namespace App\Http\Controllers;

use App\Models\categories;
use App\Models\orders;
use App\Models\products;
use Illuminate\Http\Request;

class ApiTestController extends Controller
{
    /*public function index(){
        $categories = categories::all();
        $products = products::with('category')->get();  
        $orders = orders::with(['products', 'user'])->get();

        

        return response()->json([
            'categories' => $categories,
            'products' => $products,
            'orders' => $orders
          ]);
    }*/
    public function index() {

        // build dummy data
        $categories = [
            [
            'id' => 1,
            'name' => 'Category 1'  
            ],
            [
            'id' => 2,  
            'name' => 'Category 2'
            ]
        ];
        
        $products = [
            [
            'id' => 1,
            'name' => 'Product 1',
            'category_id' => 1
            ],
            [
            'id' => 2,
            'name' => 'Product 2',
            'category_id' => 2
            ]
            
        ];

        // Dummy orders data
        $orders = [
          
            'id'=> 1,
            'user_id'=> 1,
            'products'=> [1,2] 
          
        ];
      
        return $data=response()->json([
          'categories' => $categories,
          'products' => $products,  
          'orders' => $orders
        ]);
      
      }
}
