<?php

use App\Http\Controllers\Api\Auth\GoogleController;
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

Route::get('/auth/google/url', [GoogleController::class, 'googleLoginUrl']);
Route::get('/auth/google/callback',  [GoogleController::class, 'loginCallback']);


Route::get('/', function () {
    return view('welcome');
});
