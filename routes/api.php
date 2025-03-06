<?php

use App\Models\Uf;
use App\Models\Rodovia;
use App\Http\Controllers\TrechoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/trechos', [TrechoController::class, 'index']);
Route::post('/trechos', [TrechoController::class, 'store']);
Route::get('/trechos/create', [TrechoController::class, 'create']);
Route::get('/trechos', [TrechoController::class, 'index'])->name('trechos.index');
Route::get('/trechos/{id}', [TrechoController::class, 'show']);
Route::put('/trechos/{id}', [TrechoController::class, 'update']);  
Route::delete('/trechos/{id}', [TrechoController::class, 'destroy']); 
Route::post('/api/trechos', [TrechoController::class, 'cadastrarTrecho']);
Route::get('/trechos', [TrechoController::class, 'getTrechosJson']);









Route::get('/ufs', function () {
    return response()->json(Uf::all()); 
});

Route::get('/rodovias', function () {
    return response()->json(Rodovia::all()); 
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});
