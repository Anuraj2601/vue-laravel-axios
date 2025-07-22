<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [RegisterController::class, 'login']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/store', [UserController::class, 'store'])->name('user.store');
    Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/delete/{id}', [UserController::class, 'destory'])->name('user.destroy');
    Route::get('/', [UserController::class, 'index'])->name('user.index');
});

Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/posts/create', [PostController::class, 'create'])->middleware(['permission:create_post'])->name('post.create');
    Route::post('/posts/store', [PostController::class, 'store'])->middleware(['permission:create_post'])->name('post.store');
    Route::get('/posts/edit/{id}', [PostController::class, 'edit'])->middleware(['permission:edit_post'])->name('post.edit');
    Route::put('/posts/update/{id}', [PostController::class, 'update'])->middleware(['permission:edit_post'])->name('post.update');
    Route::delete('/posts/delete/{id}', [PostController::class, 'destory'])->middleware('permission:delete_post')->name('post.destory');
    Route::get('/posts/all', [PostController::class, 'index'])->middleware('role:admin')->name('post.show');
    Route::get('/posts', [PostController::class, 'show'])->name('post.index');
    Route::post('/profile/update', [UserController::class, 'profileUpdate'])->name('profile.update');
    Route::get('/profile/{id}', [UserController::class, 'show'])->name('profile.show');
    Route::get('/list', [UserController::class, 'getCount'])->name('profile.list');
    Route::get('/posts/socmed/{id}', [PostController::class, 'postbySoc'])->name('posts.soc');
    Route::post('posts/update-all', [PostController::class, 'updateAll'])->name('posts.updates');
});

