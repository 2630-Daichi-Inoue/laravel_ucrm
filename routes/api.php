<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Customer;

Route::middleware('auth:sanctum')->get('/searchCustomers', function (Request $request) {
    return Customer::searchCustomers($request->search)->select('id', 'name', 'email', 'tel')->paginate(50);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

?>
