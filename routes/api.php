<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatMessageController;

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
/* 
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
 */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
//Route::post('/logout', [AuthController::class, 'login'])->middleware('auth:sanctum');

Route::group(['middelware' => ['auth:sanctum']], function(){
    //user
    Route::get('/user',[AuthController::class, 'usery']);
    Route::put('/user',[AuthController::class, 'update']);
    Route::post('/logout',[AuthController::class, 'logout']);
    Route::apiResource('chat',ChatController::class)->only('index','store','show');
    Route::apiResource('chat_message',ChatMessageController::class)->only('index','store');
    Route::apiResource('users',UserController::class)->only('index');
});
