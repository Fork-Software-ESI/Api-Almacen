<?php

use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\LoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/paquetes/alta', ([PaqueteController::class, 'altaPaquete']))->name('paquete.alta');
Route::patch('/paquetes/editar/{id}', ([PaqueteController::class, 'editarPaquete']))->name('paquete.editar');
Route::get('/paquetes', ([PaqueteController::class, 'listarPaquetes']))->name('paquete.listar');

Route::post('/lotes/alta', ([LoteController::class, 'altaLote']))->name('lote.alta');
Route::patch('/lotes/editar/{id}', ([LoteController::class, 'editarLote']))->name('lote.editar');
Route::get('/lotes', ([LoteController::class, 'listarLotes']))->name('lote.listar');
