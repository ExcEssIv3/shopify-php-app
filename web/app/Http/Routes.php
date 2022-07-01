<?php
 
use App\Models\Customer;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

Route::post('/customer', function (Request $request) {
    //
});

Route::get('/api/product', function () {
    // return Product::orderBy('created_at', 'asc')->get();
    return 'test';
});

Route::post('/product', function(Request $request) {
    //
});

Route::get('/product/{id}', function ($id) {
    return Variant::where('parent_id', $id)->get();
});