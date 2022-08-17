<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminsController;

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
Route::any('/logout', [AdminsController::class, "logout"])->name("logout");

Route::group(['middleware' => ['guest']], function(){
	Route::get('/', [AdminsController::class, "index"])->name("index");
	Route::post('/login', [AdminsController::class, "login"])->name("login");
});


Route::group(['middleware' => ['auth']], function(){
    Route::get('/home', [AdminsController::class, "home"])->name("home");
    Route::get('/get-all-pics', [AdminsController::class, "getAllPics"])->name("get-all-pics");
    Route::get('/approve-pic', [AdminsController::class, "approvePic"])->name("approve-pic");
});