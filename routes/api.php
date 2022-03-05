<?php

use App\Http\Controllers\ImageController;
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

Route::post('/image',[ImageController::class, 'store']);
Route::get('/image', [ImageController::class, 'lists']);
Route::put('/resize', [ImageController::class, 'resize']);
Route::delete('/image/{image}', [ImageController::class, 'delete']);
