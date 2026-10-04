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

// auth
Route::group([], function() {
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('loginProcess');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

    Route::view('/signin', 'signin')->name('signin');
    Route::post('/signin', [AuthController::class, 'signin'])->name('signinProcess');

    Route::post('/verifyEmail', [AuthController::class, 'verifyEmail'])->name('verifyEmail');
    Route::post('/resendEmail', [AuthController::class, 'resendEmail'])->name('resendEmail');

    Route::view('/forgetPassword', 'forgetPassword')->name('forgetPassword');
    Route::post('/forgetPassword', [AuthController::class, 'forgetPassword'])->name('forgetPasswordEmail');

    Route::get('/resetPassword/{auth}', [AuthController::class, 'resetPassword'])->name('resetPassword');
    Route::post('/resetPassword', [AuthController::class, 'resetPasswordProcess'])->name('resetPasswordProcess');
});

// user
Route::middleware('auth')->group(function() {
    Route::get('/user', [UserController::class, 'profile'])->name('profile');
    Route::post('/user', [UserController::class, 'update'])->name('profileUpdate');
});

// board
Route::group([], function() {
    // article
    Route::get('/', [BoardController::class, 'list'])->name('list');
    Route::get('/search', [BoardController::class, 'getArticles'])->name('getArticles');

    Route::get('/create', [BoardController::class, 'create'])->name('create')->middleware('auth');
    Route::post('/create', [BoardController::class, 'store'])->name('store')->middleware('auth');

    Route::get('/{id}', [BoardController::class, 'article'])->name('article');
    Route::post('/{id}/{type}', [BoardController::class, 'increaseCount'])
        ->where('type', "(views|likes)")
        ->name('increaseCount');

    Route::get('/{id}/edit', [BoardController::class, 'edit'])->name('edit')->middleware('auth');
    Route::match(['put', 'patch'], '/{id}/edit', [BoardController::class, 'update'])->name('update')->middleware('auth');

    Route::delete('/{id}', [BoardController::class, 'destroy'])->name('destroy')->middleware('auth');

    // reply
    Route::post('/reply/{article_id}/{parent_id?}', [ReplyController::class, 'store'])
        ->name('replyStore')
        ->whereNumber(['article_id', 'parent_id'])
        ->middleware('auth');
    Route::match(['put', 'patch'], '/reply/{id}', [ReplyController::class, 'update'])->name('replyUpdate')->middleware('auth');
    Route::delete('/reply/{id}', [ReplyController::class, 'destroy'])->name('replyDestroy')->middleware('auth');
});

// file
Route::middleware('auth')->group(function() {
    Route::get('/file/{id}', [FileController::class, 'download'])->name('download');
    Route::post('/file', [FileController::class, 'upload'])->name('upload');
    Route::delete('/file/{id}/delete', [FileController::class, 'delete'])->name('delete');
});

// test
Route::get('/test', function() {
    return "TEST HSP";
});

/*
 * 임시 디버깅 라우터
 *
Route::get('/db-check', function () {
    return [
        'host' => config('database.connections.mysql.host'),
        'database' => config('database.connections.mysql.database'),
        'username' => config('database.connections.mysql.username'),
        'actual_database' => DB::connection()->getDatabaseName(),
        'post_count' => DB::table('article')->count(),
        'hostname' => gethostname(),
        'php_sapi' => PHP_SAPI,
        'server_addr' => $_SERVER['SERVER_ADDR'] ?? null,
        'mysql_ip' => gethostbyname(
            config('database.connections.mysql.host')
        ),
    ];
});*/
