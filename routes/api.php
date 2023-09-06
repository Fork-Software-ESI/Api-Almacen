<?php

use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\EstanteriaController;
use App\Http\Controllers\AlmacenController;
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

Route::post('/paquetes', ([PaqueteController::class, 'altaPaquete']))->name('paquete.alta');
Route::patch('/paquetes/{id}', ([PaqueteController::class, 'editarPaquete']))->name('paquete.editar');
Route::get('/paquetes', ([PaqueteController::class, 'listarPaquetes']))->name('paquete.listar');
Route::get('/paquetes/{id}', ([PaqueteController::class, 'buscarPaquete']))->name('paquete.buscar');
Route::delete('/paquetes/{id}', ([PaqueteController::class, 'eliminarPaquete']))->name('paquete.eliminar');

Route::post('/lotes', ([LoteController::class, 'altaLote']))->name('lote.alta');
Route::patch('/lotes/{id}', ([LoteController::class, 'editarLote']))->name('lote.editar');
Route::get('/lotes', ([LoteController::class, 'listarLotes']))->name('lote.listar');
Route::get('/lotes/{id}', ([LoteController::class, 'buscarLote']))->name('lote.buscar');
Route::delete('/lotes/{id}', ([LoteController::class, 'eliminarLote']))->name('lote.eliminar');

Route::post('/estanterias', ([EstanteriaController::class, 'altaEstanteria']))->name('estanteria.alta');
Route::get('/estanterias/{id}', ([EstanteriaController::class, 'listarContenido']))->name('estanteria.listar');

Route::post('/almacenes', ([AlmacenController::class, 'altaAlmacen']))->name('almacen.crear');
Route::get('/almacenes/{id}', ([AlmacenController::class, 'listarEstanterias']))->name('almacen.listar');