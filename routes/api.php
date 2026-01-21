<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Customer;
use App\Http\Controllers\Api\AnalysisController;

Route::middleware('auth:sanctum')->get('/analysis', [ AnalysisController::class, 'index' ])->name('api.analysis');

Route::middleware('auth:sanctum')->get('/searchCustomers', function (Request $request) {
    return Customer::searchCustomers($request->search)->select('id', 'name', 'email', 'tel')->paginate(50);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

?>
