<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [App\Http\Controllers\GameController::class, 'startGame']);


Route::get('/test', function () {
    echo "works";
});

//Route::get('/test', function () {
//    echo "works";
//});
//
//
//Route::post('/game', [App\Http\Controllers\GameController::class, 'game'])->name('game');