<?php

use App\Http\Controllers\ApiTestController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

//--------test my api-------------------
Route::get('/test', function (){
    return view('test');
});

Route::get('/index', [ApiTestController::class, 'index']);

/*
//------------auth route---------------------
Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);
*/
