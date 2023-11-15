<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\GerenteController;
use App\Http\Controllers\ChoferController;

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

Route::prefix('gerente')->group(function () {
    Route::post('/paquetes', [GerenteController::class, 'crearPaquete']);
    Route::get('/paquetes/{ID_Almacen}', [GerenteController::class, 'listarPaquetesAlmacen']);
    Route::get('/paquetes', [GerenteController::class, 'buscarPaquete']);
    Route::patch('/paquetes', [GerenteController::class, 'editarPaquete']);
    Route::delete('/paquetes/{id}', [GerenteController::class, 'eliminarPaquete']);

    Route::post('/lotes', [GerenteController::class, 'crearLote']);
    Route::get('/lotes', [GerenteController::class, 'listarLotes']);
    Route::get('/lotes/buscar', [GerenteController::class, 'buscarLote']);
    Route::post('/lotes/asignar', [GerenteController::class, 'asignarPaqueteLote']);
    Route::patch('/lotes', [GerenteController::class, 'editarLote']);
    Route::delete('/lotes/{id}', [GerenteController::class, 'eliminarLote']);


    Route::get('/choferes', [GerenteController::class, 'verChoferes']);
    Route::get('/choferes/disponibles', [GerenteController::class, 'verChoferesDisponibles']);
    Route::get('/choferes/ocupados', [GerenteController::class, 'listarChoferesCamiones']);

    Route::get('/camiones', [GerenteController::class, 'verCamiones']);
    Route::get('/camiones/libres', [GerenteController::class, 'verCamionesSinChofer']);
    Route::get('/camiones/disponibles', [GerenteController::class, 'verCamionesDisponibles']);
    Route::post('/camiones', [GerenteController::class, 'asignarChoferCamion']);
    Route::post('/camiones/lote', [GerenteController::class, 'asignarLoteCamion']);
    Route::get('/camiones/plataformas', [GerenteController::class, 'verCamionesEnPlataformas']);
    Route::get('/camiones/transito', [GerenteController::class, 'verCamionesEnTransito']);
    Route::patch('/camiones', [GerenteController::class, 'marcarCamionComoPreparado']);
});

Route::prefix('funcionario')->group(function () {
    Route::post('/estante', [FuncionarioController::class, 'registrarPaqueteEstante']);
    Route::patch('/estante', [FuncionarioController::class, 'trasladarPaqueteEstante']);
    Route::delete('/estante', [FuncionarioController::class, 'quitarPaqueteDeEstante']);

    Route::get('/paquetes', [FuncionarioController::class, 'listarPaquetesAlmacen']);

    Route::get('/lotes', [FuncionarioController::class, 'listarLotes']);
    Route::get('/lotes/paquetes', [FuncionarioController::class, 'listarPaqueteLote']);
    Route::patch('/lotes/paquetes', [FuncionarioController::class, 'actualizarPaqueteLote']);
    Route::post('/lotes', [FuncionarioController::class, 'cargarLoteCamion']);
});

Route::prefix('chofer')->group(function () {
    Route::get('/camion/{id}', [ChoferController::class, 'verContenidoCamion']);
    Route::post('/camion/marcarHora', [ChoferController::class, 'marcarHora']);
    Route::post('/camion/liberar', [ChoferController::class, 'liberarCamion']);
    Route::post('/camion/estado', [ChoferController::class, 'estadoCamion']);
    Route::post('/camion/entregar', [ChoferController::class, 'paqueteEntregado']);
});
