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

Route::post('/paquetes', [GerenteController::class, 'crearPaquete']);
Route::get('/paquetes/{id}', [GerenteController::class, 'listarPaquetesAlmacen']);
Route::get('/paquetes', [GerenteController::class, 'buscarPaquete']);
Route::patch('/paquetes', [GerenteController::class, 'editarPaquete']);
Route::delete('/paquetes/{id}', [GerenteController::class, 'eliminarPaquete']);

Route::post('/paquetes/estante', [GerenteController::class, 'registrarPaqueteEstante']);
Route::patch('/paquetes/estante', [GerenteController::class, 'trasladarPaqueteEstante']);
Route::delete('/paquetes/estante', [GerenteController::class, 'quitarPaqueteDeEstante']);

Route::post('/lotes', [GerenteController::class, 'crearLote']);
Route::get('/lotes', [GerenteController::class, 'listarLotes']);
Route::post('/lotes/asignar', [GerenteController::class, 'asignarPaqueteLote']);
Route::patch('/lotes', [GerenteController::class, 'editarLote']);
Route::delete('/lotes/{id}', [GerenteController::class, 'eliminarLote']);

Route::get('/choferes', [GerenteController::class, 'verChoferes']);
Route::get('/choferes/disponibles', [GerenteController::class, 'verChoferesDisponibles']);
Route::get('/choferes/ocupados', [GerenteController::class, 'listarChoferesCamiones']);

Route::get('/camiones', [GerenteController::class, 'verCamiones']);
Route::get('/camiones/sinchofer', [GerenteController::class, 'verCamionesSinChofer']);
Route::get('/camiones/disponibles', [GerenteController::class, 'verCamionesDisponibles']);
Route::post('/camiones/asignar', [GerenteController::class, 'asignarChoferCamion']);
Route::post('/camiones/plataformas', [GerenteController::class, 'verCamionesEnPlataformas']);
Route::get('/camiones/transito', [GerenteController::class, 'verCamionesEnTransito']);
