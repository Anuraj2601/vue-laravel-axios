<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SocialMediaController;
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

Route::post('/register', [RegisterController::class, 'store']);     // register and create user
Route::post('/login', [RegisterController::class, 'login']);        // login
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']); // Forgot password send reset link
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']); // reset password

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/edit/{id}', [UserController::class, 'edit'])->middleware('permission:edit_user')->name('user.edit');       //  fetch user with roles for edit
    Route::put('/update/{id}', [UserController::class, 'update'])->middleware('permission:edit_user')->name('user.update');     // update user
    Route::delete('/delete/{id}', [UserController::class, 'destory'])->middleware('permission:delete_user')->name('user.destroy');    // delete user
    Route::get('/', [UserController::class, 'index'])->middleware('permission:manage_users')->name('user.index');  //fetch all users
    
    Route::get('/roles', [RoleController::class, 'index'])->middleware(['permission:manage_roles|create_user|edit_user'])->name('roles.index');  // fetch all roles
    Route::get('/roles-by-user-create', [RoleController::class, 'getUserRolesforCreate'])->middleware(['permission:manage_roles|create_user|edit_user'])->name('user-create.role');
    Route::get('role/{id}', [RoleController::class, 'show'])->middleware('permission:edit_role')->name('role.show');  // fetch role for edit
    Route::get('/permissions', [PermissionController::class, 'index'])->middleware(['permission:manage_permissions'])->name('permission.index'); // fetch all permissions
    Route::get('/permission/{id}', [PermissionController::class, 'getPermissionById'])->middleware('permission:edit_permission')->name('permission.show'); // fetch permission detail for edit

    Route::put('/user/language', [UserController::class, 'updateLanguage'])->name('user.lang'); // update user language
    Route::get('/fetchUser', [UserController::class, 'fetchUser'])->name('user.fetch'); // fetch particular user detail after state changes

    Route::get('/posts/create', [PostController::class, 'create'])->middleware(['permission:create_social_media|create_post'])->name('post.create'); // fetch tags,social media for create socialmedia & posts
   
    Route::post('/profile/update', [UserController::class, 'profileUpdate'])->name('profile.update'); // update profile info
    Route::get('/profile/{id}', [UserController::class, 'show'])->name('profile.show'); // get profile info
    Route::get('/list', [UserController::class, 'getCount'])->name('profile.list'); // get count for dashboard info

    Route::get('/posts/socmed/{id}', [PostController::class, 'postbySoc'])->middleware('permission:manage_social_media|edit_social_media')->name('posts.soc'); // fetch posts for specific social media by id
    Route::get('/posts/socmeduser/{id}', [SocialMediaController::class, 'SocPostsByUser'])->middleware('permission:manage_my_posts')->name('posts.socuser'); // fetch posts for specific user
    Route::get('/social-media/{id}', [SocialMediaController::class, 'show'])->middleware('permission:manage_social_media|edit_social_media|manage_my_posts')->name('socialmedia.show'); // fecth social media by id
    Route::get('/social-media', [SocialMediaController::class,'fetchSocialMediaByUser'])->middleware('permission:manage_my_posts|edit_post')->name('socialmedia'); // fetch user posts with all social media
    Route::get('/social-media-create/user', [SocialMediaController::class, 'fetchByUser'])->middleware('permission:manage_my_posts'); // fetch Social media by created user
    Route::post('/social-media-create/mypost', [SocialMediaController::class,  'storeUserSocialMediaWithPosts'])->middleware('permission:manage_my_posts'); // myposts create
    Route::post('/social-media/create', [SocialMediaController::class, 'storeWithPosts'])->middleware('permission:create_social_media')->name('socialmedia.store'); // create new social media
    Route::post('/social-media/update', [SocialMediaController::class, 'updateWithPosts'])->middleware(['permission:edit_social_media', 'permission: edit_post|delete_post|create_post'])->name('socialmedia.updte'); // update social media , update posts & create posts , delete posts
    Route::delete('/social-media/delete', [SocialMediaController::class, 'deleteSocialMedia'])->middleware(['permission:delete_social_media'])->name('socialmedia.delete'); // delete social media
    Route::post('/social-media-user/update', [SocialMediaController::class, 'UpdateUserPosts'])->middleware(['permission:manage_my_posts','permission: edit_post|delete_post|create_post'])->name('socialmediauser.update'); // (Manage own posts) update user_posts , create posts, delete posts

    Route::post('/roles/create', [RoleController::class, 'storeOrUpdate'])->middleware('permission:create_role|edit_role')->name('role.create'); // create & edit roles
    Route::delete('/roles/delete/{id}', [RoleController::class, 'deleteRole'])->middleware('permission:delete_role')->name('role.delete'); // delete role
    Route::post('/permissions/create', [PermissionController::class, 'storeWithUpdate'])->middleware('permission:create_permission|edit_permission')->name('permissions.create'); // create & edit permissions
    Route::delete('/permissions/delete/{id}', [PermissionController::class, 'deletePermission'])->middleware('permission:delete_permission')->name('permissions.delete'); // delete permission
});

Route::middleware(['auth:sanctum'])->group(function() {
     /* Route::post('/posts/store', [PostController::class, 'store'])->middleware(['permission:create_post'])->name('post.store'); */
   /*  Route::get('/posts/edit/{id}', [PostController::class, 'edit'])->middleware(['permission:edit_post'])->name('post.edit'); */
   /*  Route::put('/posts/update/{id}', [PostController::class, 'update'])->middleware(['permission:edit_post'])->name('post.update'); */
    /* Route::post('posts/update-all', [PostController::class, 'updateAll'])->name('posts.updates'); */
    /* Route::delete('/posts/delete/{id}', [PostController::class, 'destory'])->middleware('permission:delete_post')->name('post.destory'); */
    /* Route::get('/posts/all', [PostController::class, 'index'])->name('post.show'); */
    /* Route::get('/posts', [PostController::class, 'show'])->name('post.index'); */

        /* Route::get('/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/store', [UserController::class, 'store'])->name('user.store'); */
});

