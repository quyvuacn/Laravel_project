<?php

use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\ShopController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Front\CheckOutController;

Route::get('/',[HomeController::class,'index']);
Route::prefix('shop')->group(function (){
    Route::get('/',[ShopController::class,'index']);

    Route::get('product/{id}',[ShopController::class,'show']);

    Route::post('product/{id}',[ShopController::class,'postComment']);

    Route::get('{categoryName}',[ShopController::class,'category']);

});


Route::prefix('cart')->group(function (){
    Route::get('/add/{id}',[CartController::class,'add']);

    Route::get('/',[CartController::class,'index']);
    Route::get('delete/{rowId}',[CartController::class,'delete']);
    Route::get('update',[CartController::class,'update']);

});

Route::prefix('checkout')->group(function (){
    Route::get('/',[CheckOutController::class,'index']);
    Route::post('/',[CheckOutController::class,'addOrder']);
    Route::get('/vnPaycheck',[CheckOutController::class,'vnPayCheck']);
    Route::get('/result',[CheckOutController::class,'result']);
});
