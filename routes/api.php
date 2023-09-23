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

Route::post('/login','App\Http\Controllers\AuthController@login');

Route::get('/verPaquete', [GerenteController::class, 'verPaquete'])->name('gerente.verPaquete');

Route::get('/verLote', [GerenteController::class, 'verLote'])->name('gerente.verLote');

Route::get('/verCamion', [GerenteController::class, 'verCamion'])->name('gerente.verCamion');

Route::get('/verChofer', [GerenteController::class, 'verChofer'])->name('gerente.verChofer');

Route::get('/verLoteCamion', [GerenteController::class, 'verLoteCamion'])->name('gerente.verLoteCamion');

Route::get('/verPaqueteLote', [GerenteController::class, 'verPaqueteLote'])->name('gerente.verPaqueteLote');

Route::get('/verChoferCamion', [GerenteController::class, 'verChoferCamion'])->name('gerente.verChoferCamion');

Route::post('/crearPaquete', [GerenteController::class, 'crearPaquete'])->name('gerente.crearPaquete');

Route::post('/crearLote', [GerenteController::class, 'crearLote'])->name('gerente.crearLote');

Route::post('/loteCamion',[GerenteController::class, 'loteCamion'])->name('gerente.loteCamion');;

Route::post('/paquteLote', [GerenteController::class, 'paqueteLote'])->name('gerente.paqueteLote');

Route::post('/choferCamion', [GerenteController::class, 'choferCamion'])->name('funcionario.camionChofer');

//

Route::get('/verPaquete', [FuncionarioController::class, 'verPaquete'])->name('funcionario.verPaquete');

Route::get('/verLote', [FuncionarioController::class, 'verLote'])->name('funcionario.verLote');

Route::get('/verPaqueteLote', [FuncionarioController::class, 'verPaqueteLote'])->name('funcionario.verPaqueteLote');

Route::get('/verEstante', [FuncionarioController::class, 'verEstante'])->name('funcionario.verEstante');

Route::get('/verPaqueteEstante', [FuncionarioController::class, 'verPaqueteEstante'])->name('funcionario.verPaqueteEstante');

Route::get('/verLoteCamion', [FuncionarioController::class, 'verLoteCamion'])->name('funcionario.verLoteCamion');

Route::post('/paqueteEstante', [FuncionarioController::class, 'paqueteEstante'])->name('funcionario.paqueteEstante');

Route::post('/paqueteLote', [FuncionarioController::class, 'paqueteLote'])->name('funcionario.paqueteLote');

Route::post('/loteCamion', [FuncionarioController::class, 'loteCamion'])->name('funcionario.loteCamion');

