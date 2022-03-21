<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix'=>'v1','middleware' => 'api'], function($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/profile', [AuthController::class, 'profile']);

    Route::get('/books/fetch', [BookController::class, 'index'])->middleware('auth:api');
    Route::post('/book/create', [BookController::class, 'store'])->middleware('auth:api');
    Route::get('/book/fetch/{book}', [BookController::class, 'fetchBookId'])->middleware('auth:api');
    Route::patch('/book/update/{bookId}', [BookController::class, 'updateBook'])->middleware('auth:api');
    Route::delete('/book/delete/{bookId}', [BookController::class, 'deleteBook'])->middleware('auth:api');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->middleware('auth:api');

});
