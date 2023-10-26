<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\GerenteController;

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

Route::prefix('/v1')->group(function () {
    Route::post('/paquete', [GerenteController::class, 'crearPaquete']);
    Route::get('/paquetes', [GerenteController::class, 'listarPaquetes']);
    Route::get('/paquete', [GerenteController::class, 'buscarPaquete']);
    Route::post('/paquete/estante', [GerenteController::class, 'registrarPaqueteEstante']);
    Route::patch('/paquete/estante', [GerenteController::class, 'trasladarPaqueteEstante']);
    Route::delete('/paquete/estante', [GerenteController::class, 'quitarPaqueteDeEstante']);
});
