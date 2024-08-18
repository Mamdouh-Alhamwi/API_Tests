<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrdersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



/*Route::get('/user', function (Request $request) {
    return $request->user();
});*/

// routes/api.php

Route::group(['prefix' => 'admin'], function () {
    Route::post('/register', [AuthController::class, 'registerAdmin']);
});

Route::group(['prefix' => 'admin', 'middleware' => 'auth:sanctum'], function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    /*//register as admin
    Route::post('/register', [AuthController::class, 'registerAdmin']);
    */
    
    //login as admin
    Route::post('/login', [AuthController::class, 'loginAdmin']);

    //delete user
    Route::delete('/users/{id}', [AuthController::class, 'destroyAdmin']);
    

    // Products API routes
    Route::get('/products', [AuthController::class, 'indexProducts']);
    Route::get('/products/{product}', [AuthController::class, 'showProduct']);
    Route::post('/products', [AuthController::class, 'createProduct']);
    Route::put('/products/{product}', [AuthController::class, 'updateProduct']);
    Route::delete('/products/{product}', [AuthController::class, 'destroyProduct']);

    // Categories API routes
    Route::get('/categories', [AuthController::class, 'indexCategories']);
    Route::get('/categories/{category}', [AuthController::class, 'showCategory']);
    Route::post('/categories', [AuthController::class, 'createCategory']);
    Route::put('/categories/{category}', [AuthController::class, 'updateCategory']);
    Route::delete('/categories/{category}', [AuthController::class, 'destroyCategory']);
    Route::get('/categories/{category}/products', [AuthController::class, 'categoryProducts']);
    Route::put('/categories/{category}/assign/products/{product}', [AuthController::class, 'assignProductToCategory']);
    Route::delete('/categories/{category}/products/{product}', [AuthController::class, 'removeProductFromCategory']);

});


    //User auth
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::delete('/users/{id}', [AuthController::class, 'destroy']);



//User orders
Route::middleware('auth:sanctum')->group(function(){
    Route::get('/orders', [OrdersController::class, 'indexOrder']);
    Route::get('/orders/{order}', [OrdersController::class, 'showOrder']);
    Route::post('/orders', [OrdersController::class, 'storeOrder']);
    Route::put('/orders/{order}', [OrdersController::class, 'updateOrder']);
    Route::delete('/orders/{order}', [OrdersController::class, 'deleteOrder']);
});
