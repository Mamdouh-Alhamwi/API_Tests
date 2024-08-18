<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\categories;
use App\Models\products;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    
    //--------------------------Users---------------------------------------
    
    public function register(Request $request){
        $data = $request->validate([
            'name'=>['required', 'max:255', 'unique:users'],
            'email'=>['required', 'max:255', 'email', 'unique:users'],
            'password' => ['required','min:8'],
        ]);
        
        // Add the default role to the validated data
        $data['role'] = UserRole::USER;

        $user = User::create($data);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];

    }

    public function login(Request $request){
        $data = $request->validate([
            'name'=>['required', 'max:255'],
            'password' => ['required','min:8'],
        ]);

        /*if (!User::where('name', $data['name'])->exists()) {
            return response([
                'message' => 'User not found'
            ], 404);
        }*/

        $user = User::where('name', $data['name'])->first();
        

        if(!$user || !Hash::check($data['password'], $user->password)){
            return response([
                'message' => 'wrong info'
            ], 401);
        }

        // Check the user's role
        if ($user->role === UserRole::ADMIN) {
            // Return a token with admin privileges
            $token = $user->createToken($user->name, ['admin'])->plainTextToken;
        } 
        else {
            // Return a token with user privileges
            $token = $user->createToken($user->name)->plainTextToken;
        }

        //$token = $user->createToken($user->name)->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];

    }
    public function destroy( int $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json(['message' => 'User deleted successfully']);
        } 
        else {
            return response()->json(['message' => 'User not found'], 404);
        }
    }


    // ---------------------- Admin-only API for products ------------------------------------------

    public function indexProducts()
    {
        // Return a list of all products
        $products = products::all();
        return response()->json($products);
    }

    public function showProduct(products $product)
    {
        // Return a single product
        return response()->json($product);
    }

    public function createProduct(Request $request)
    {
        // Validate request data
        $data = $request->validate([
            'name' => ['required', 'max:255'],

            'categories_id' => ['required', 'integer'],
            'categories_id.*' => ['exists:categories,id'], // validate each category ID
            
            'price' => ['required','numeric'],
            /*'image' => ['nullable', 'image', 'max:10240', 
            'mimes:png,jpg,jpeg,webp,avif,jfif'],*/
            'image' => ['nullable', function ($attribute, $value, $fail) {
            if ($value instanceof UploadedFile) {
                // Validate file upload
                if (!$value->isValid()) {
                    $fail('The image field must be a valid file.');
                }
                if (!$value->mimeTypes($value->getClientOriginalExtension())) {
                    $fail('The image field must be a file of type: png, jpg, jpeg, webp, avif, jfif.');
                }
            } elseif (filter_var($value, FILTER_VALIDATE_URL)) {
                // Validate image URL
                $headers = @get_headers($value);
                if (!strpos($headers[0], '200')) {
                    $fail('The image URL is not valid.');
                }
            } else {
                $fail('The image field must be a valid file or URL.');
            }
        }],
        ]);

        

        // Create a new product
        $product = new products();
        $product->name = $data['name'];
        $product->price = $data['price'];
        $product->categories_id = $data['categories_id'];
        $product->image = $data['image'];

        // Save the product
        $product->save();
        //$product = products::create($data);

        // Sync categories with the product
        $product->categories()->associate($data['categories_id']);
        

        return response()->json(['message' => 'Product created successfully'], 201);
    }

    public function updateProduct(Request $request, products $product)
    {
        // Validate request data
        $data = $request->validate([
            'name' => ['required', 'max:255'],
            
            'categories_id' => ['required', 'integer', 'exists:categories,id'],
            'categories_id.*' => ['exists:categories,id'], // validate each category ID

            'price' => ['required', 'numeric'],
            /*'image' => ['nullable', 'image', 'max:10240', 
            'mimes:png,jpg,jpeg,webp,avif,jfif'],*/
            'image' => ['nullable', function ($attribute, $value, $fail) {
            if ($value instanceof UploadedFile) {
                // Validate file upload
                if (!$value->isValid()) {
                    $fail('The image field must be a valid file.');
                }
                if (!$value->mimeTypes($value->getClientOriginalExtension())) {
                    $fail('The image field must be a file of type: png, jpg, jpeg, webp, avif, jfif.');
                }
            } elseif (filter_var($value, FILTER_VALIDATE_URL)) {
                // Validate image URL
                $headers = @get_headers($value);
                if (!strpos($headers[0], '200')) {
                    $fail('The image URL is not valid.');
                }
            } else {
                $fail('The image field must be a valid file or URL.');
            }
        }],
        ]);

        // Update the product
        $product->name = $data['name'];
        $product->price = $data['price'];
        $product->categories_id = $data['categories_id'];
        $product->image = $data['image'];
        $product->save();
        //$product->update($data);
        
        // Sync categories with the product
        $product->categories()->associate($data['categories_id']);


        return response()->json(['message' => 'Product updated successfully']);
    }

    public function destroyProduct(products $product)
    {
        // Delete a product
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }

    // ---------------------- Admin-only API for categories ------------------------------------------

public function indexCategories()
    {
        // Return a list of all categories
        $categories = categories::all();
        return response()->json($categories);
    }

public function showCategory(categories $category)
    {
        // Return a single category
        return response()->json($category);
    }

public function createCategory(Request $request)
    {
        // Validate request data
        $data = $request->validate([
            'name' => ['required', 'max:255'],
        ]);

    // Create a new category
    $category = categories::create($data);

    return response()->json(['message' => 'Category created successfully'], 201);
    }

public function updateCategory(Request $request, categories $category)
    {
        // Validate request data
        $data = $request->validate([
            'name' => ['required', 'max:255'],
        ]);

        // Update the category
        $category->update($data);

        return response()->json(['message' => 'Category updated successfully']);
    }

public function destroyCategory(categories $category)
    {
        // Delete a category
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }

public function categoryProducts(categories $category)
    {
        // Return a list of products associated with a category
        $products = $category->products;
        return response()->json($products);
    }

public function assignProductToCategory(Request $request, categories $category, products $product)
    {

        // Update the product category
        $product->categories()->associate($category);
        $product->save();

        return response()->json([
            'message' => 'Product category updated successfully',
        ], 200);
    /* // Validate request data
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

    /* return response()->json([
            'data' => [
            'category' => $category,
            'product' => $product
            ]  
        ], 200);*/
    /*
        // Assign a product to a category
        $category->products()->attach($product->id);

        return response()->json([
            'message' => 'Product assigned to category successfully',
        ]);*/
    }

public function removeProductFromCategory(Request $request, categories $category, products $product)
    {
        /*
        // Validate request data
        $data = $request->validate([
            'products_id' => ['required', 'exists:products,id'],
        ]);
        */

        // Remove a product from a category
        /*$product->category()->dissociate();
        $product->save();*/
        $category->products()->where('id', $product->id)->delete();
        //$category->products()->detach($product->id);

        /*
        // Remove a product from a category
        $category->products()->detach($data['products_id']);
        */

        return response()->json(['message' => 'Product removed from category successfully']);
    }

    //-------------------------Admin Auth-----------------------------------
public function registerAdmin(Request $request) {
    // Register admin user logic
    $data = $request->validate([
        'name'=>['required', 'max:255', 'unique:users'],
        'email'=>['required', 'max:255', 'email', 'unique:users'],
        'password' => ['required','min:8'],
    ]);
    
    // Add the default role to the validated data
    $data['role'] = UserRole::ADMIN;

    $user = User::create($data);

    $token = $user->createToken('auth_token')->plainTextToken;

    return [
        'user' => $user,
        'token' => $token
    ];


}

public function loginAdmin(Request $request) {
    // Login admin user logic
    $data = $request->validate([
        'name'=>['required', 'max:255'],
        'password' => ['required','min:8'],
    ]);

    /*if (!User::where('name', $data['name'])->exists()) {
        return response([
            'message' => 'User not found'
        ], 404);
    }*/

    $user = User::where('name', $data['name'])->first();
    

    if(!$user || !Hash::check($data['password'], $user->password)){
        return response([
            'message' => 'wrong info'
        ], 401);
    }

    // Check the user's role
    if ($user->role === UserRole::ADMIN) {
        // Return a token with admin privileges
        $token = $user->createToken($user->name, ['admin'])->plainTextToken;
    } 
    else {
        // Return a token with user privileges
        $token = $user->createToken($user->name)->plainTextToken;
    }

    //$token = $user->createToken($user->name)->plainTextToken;

    return [
        'user' => $user,
        'token' => $token
    ];
}

public function destroyAdmin( int $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json(['message' => 'User deleted successfully']);
        } 
        else {
            return response()->json(['message' => 'User not found'], 404);
        }
}

}



