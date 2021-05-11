<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\educarController;

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

Route::get('crearUsuario', [educarController::class,'create'])->name('registro');
Route::get('listarUsuarios', [educarController::class,'index'])->name('listarUsuarios');
Route::get('filtroUsuario', [educarController::class,'show'])->name('listarFiltro');
Route::get('filtroDoble', [educarController::class,'showTwoParameters'])->name('listarFiltroDoble');
Route::get('recarga', [educarController::class,'reloadCount'])->name('recarga');
Route::get('transferencia', [educarController::class,'transfer'])->name('transferencia');
Route::get('listarCargos', [educarController::class,'position'])->name('cargos');
Route::get('gastos', [educarController::class,'spending'])->name('gastos');