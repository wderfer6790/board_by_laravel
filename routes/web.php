<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{AuthController, BoardController, ReplyController, FileController};

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

Route::get('/', function () {
    return view('login');
});

// auth
Route::group([
//    'as' => 'auth', 'prefix' => 'auth'
], function() {
    Route::view('/login', 'login')->name('login');
    Route::post('/auth', [AuthController::class, 'login'])->name('login');
    Route::delete('/auth', [AuthController::class, 'logout'])->name('logout');
});

// board
Route::group([
//    'as' => 'board', 'prefix' => 'board'
], function() {
    // article
    Route::get('/', [BoardController::class, 'list'])->name('list');
    Route::get('/search', [BoardController::class, 'getArticles'])->name('getArticles');

    Route::get('/create', [BoardController::class, 'create'])->name('create');
    Route::post('/create', [BoardController::class, 'store'])->name('store');

    Route::get('/{id}', [BoardController::class, 'article'])->name('article');

    Route::get('/{id}/edit', [BoardController::class, 'edit'])->name('edit');
    Route::match(['put', 'patch'], '/{id}/edit', [BoardController::class, 'update'])->name('update');

    Route::delete('/{id}', [BoardController::class, 'destroy'])->name('destroy');

    // reply
    Route::post('/reply/{article_id}', [ReplyController::class, 'store'])->name('reply');
    Route::match(['put', 'patch'], '/reply/{id}', [ReplyController::class, 'store'])->name('replyUpdate');
    Route::delete('/reply/{id}', [ReplyController::class, 'store'])->name('replyDestroy')->name('replyDestroy');
});

// file
Route::group([

], function() {
    Route::get('/file', [FileController::class, 'download'])->name('download');
    Route::post('/file', [FileController::class, 'upload'])->name('upload');
    Route::delete('/file', [FileController::class, 'delete'])->name('delete');
});
