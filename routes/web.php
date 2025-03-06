 <?php

use App\Models\Uf;
use App\Models\Rodovia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeuController;
use App\Http\Controllers\TrechoController;


Route::get('/', [MeuController::class, 'index']);
Route::get('/trechos', [TrechoController::class, 'index']);
Route::post('/trechos', [TrechoController::class, 'cadastrarTrecho']);
Route::get('/trechos', [TrechoController::class, 'index'])->name('trechos.index');





Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
