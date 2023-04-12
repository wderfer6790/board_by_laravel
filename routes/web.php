<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{AuthController, UserController, BoardController, ReplyController, FileController};

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
Route::group([], function() {
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('loginProcess');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::view('/signin', 'signin')->name('signin');
    Route::post('/signin', [AuthController::class, 'signin'])->name('signinProcess');

    Route::post('/verifyEmail', [AuthController::class, 'verifyEmail'])->name('verifyEmail');
    Route::post('/resendEmail', [AuthController::class, 'resendEmail'])->name('resendEmail');
});

// user
Route::group([], function() {
    Route::get('/user', [UserController::class, 'profile'])->name('profile');
    Route::post('/user', [UserController::class, 'update'])->name('profileUpdate');
});

// board
Route::group([], function() {
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
    Route::post('/reply/{article_id}/{parent_id?}', [ReplyController::class, 'store'])
        ->name('replyStore')
        ->whereNumber(['article_id', 'parent_id']);
    Route::match(['put', 'patch'], '/reply/{id}', [ReplyController::class, 'update`'])->name('replyUpdate');
    Route::delete('/reply/{id}', [ReplyController::class, 'destroy'])->name('replyDestroy');
});

// file
Route::group([], function() {
    Route::get('/file/{id}', [FileController::class, 'download'])->name('download');
    Route::post('/file', [FileController::class, 'upload'])->name('upload');
    Route::delete('/file/{id}/delete', [FileController::class, 'delete'])->name('delete');
});
