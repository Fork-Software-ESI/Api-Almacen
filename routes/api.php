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

Route::post('/crearPaquete', [GerenteController::class, 'crearPaquete'])->name('gerente.crearPaquete');

Route::post('/crearLote', [GerenteController::class, 'crearLote'])->name('gerente.crearLote');

Route::post('/choferCamion', [GerenteController::class, 'choferCamion'])->name('funcionario.camionChofer');

//

Route::post('/paqueteEstante', [FuncionarioController::class, 'paqueteEstante'])->name('funcionario.paqueteEstante');

Route::post('/paqueteLote', [FuncionarioController::class, 'paqueteLote'])->name('funcionario.paqueteLote');

Route::post('/loteCamion', [FuncionarioController::class, 'loteCamion'])->name('funcionario.loteCamion');

