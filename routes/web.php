<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Ets2Controller;
use App\Http\Controllers\GtaivController;
use App\Http\Controllers\GtasaController;
use App\Http\Controllers\GtavController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\Models3dController;
use App\Http\Controllers\ModsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\StarController;
use App\Http\Controllers\UserController;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

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



// Route::post('user/create', 'UserController@create')->name('create-user');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->middleware('verify_host')->name('home');

Route::any('/', [ModsController::class, 'index'])->middleware('verify_host')->name('index');


/*rotas categorias*/
Route::group(['prefix'=>'category/mods'], function(){
    /*carrega estrutura*/
    Route::get('', [CategoryController::class, 'index'])->name('category-index');
    Route::post('/create', [CategoryController::class, 'create'])->name('category-struture-create');
    Route::post('/edit', [CategoryController::class, 'edit'])->name('category-struture-edit');
    
    /**/
    Route::post('/create', [CategoryController::class, 'create'])->name('category-create');
    Route::post('/edit', [CategoryController::class, 'edit'])->name('category-edit');
    Route::post('/delete', [CategoryController::class, 'delete'])->name('category-delete');
});

// route::get('teste', function(){
    
//     $teste = Storage::allFiles('mods/images');
//     $teste1 = Storage::allFiles('user/img/perfil');
//     $teste2 = Storage::allFiles('images/mods-principal');
    
//     Storage::delete($teste);
//     Storage::delete($teste1);
//     Storage::delete($teste2);
// });


/*rotas de mods*/
Route::group(['prefix'=>'mods','middleware'=> ['verify_host']], function(){
    Route::get('', [ModsController::class , 'index'])->name('mods-index');

    Route::post('approved', [ModsController::class , 'approvedMod'])->middleware(['auth', 'verified', 'user_block'])->name('mods-approved');
    Route::get('mymods', [ModsController::class , 'myMods'])->middleware(['auth', 'verified', 'user_block'])->name('mod-my-mods');
    Route::get('/detail/{id}/{name?}', [ModsController::class , 'detail'])->name('mods-detail');
    
    Route::post('/store/images', [ModsController::class , 'imageStorage'])->middleware(['auth', 'verified', 'user_block'])->name('mods-store-images');
    
    Route::get('/struture/create', [ModsController::class , 'getStrutureCreate'])->middleware(['auth', 'verified', 'user_block'])->name('mod-create-struture');
    
    Route::any('/getcategories', [ModsController::class , 'getCategories'])->middleware(['auth', 'verified', 'user_block'])->name('get-categories');
    Route::any('/gettags', [ModsController::class , 'getTags'])->middleware(['auth', 'verified', 'user_block'])->name('get-tags');

    Route::post('/download/{id}', [ModsController::class , 'download'])->name('download');

    Route::post('/create', [ModsController::class , 'create'])->middleware(['auth', 'verified', 'user_block'])->name('mods-create');
    Route::get('/edit/{id}', [ModsController::class , 'getStrutureEdit'])->middleware(['auth', 'verified', 'user_block'])->name('mods-edit');
    Route::post('/update/{id}', [ModsController::class , 'edit'])->middleware(['auth', 'verified', 'user_block'])->name('mods-update');
    Route::post('/delete/{id}', [ModsController::class , 'delete'])->middleware(['auth', 'verified', 'user_block'])->name('mods-delete');
    Route::delete('/delete/images/{id}', [ModsController::class , 'deleteImage'])->middleware(['auth', 'verified', 'user_block'])->name('mods-images-delete');
});

// Rotas para administração do sistema
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'verified', 'verify_host', 'user_block']], function () {
    Route::get('', [AdminController::class, 'index'])->name('admin-index');

    Route::get('/mods/approved', [AdminController::class, 'approved'])->name('mod-approved');
    Route::get('/mods/not/approved', [AdminController::class, 'notApproved'])->name('mod-not-approved');

    Route::post('/user/disable/{id}', [UserController::class, 'disableUser'])->name('admin-user-disable');
    Route::post('/user/active/{id}', [UserController::class, 'activeUser'])->name('admin-user-active');

    Route::get('/listusers', [UserController::class, 'getStrutureUsers'])->name('admin-listusers');
});

// Rotas para gerenciamento de usuários
Route::group(['prefix' => 'user', 'middleware' => ['auth', 'verified', 'verify_host', 'user_block']], function () {
    Route::get('', [UserController::class, 'index'])->name('user-index');

    Route::post('update/image', [UserController::class, 'updateImage'])->name('user-image-update');
    
    Route::post('update/password', [UserController::class, 'updatePassword'])->name('user-password-update');

    Route::get('/create', [UserController::class, 'getStrutureCreate'])->name('user-create');
    Route::get('/edit', [UserController::class, 'getStrutureEdit'])->name('user-edit');
    Route::get('/profile', [UserController::class, 'getStrutureEdit'])->name('user-profile');
});



/*Rotas comentarios*/
// Rotas para manipulação de comentários
Route::group(['prefix' => 'comments', 'middleware' => ['auth', 'verified', 'verify_host', 'user_block']], function () {
    Route::post('/create', [CommentController::class, 'create'])->name('comments-create');
    Route::get('/edit/{id}', [CommentController::class, 'edit'])->name('comments-edit');
    Route::post('/delete/{user_id}/{id}/{id_mod}/', [CommentController::class, 'delete'])->name('comments-delete');
});

// Rotas para manipulação de notificações
Route::group(['prefix' => 'notification', 'middleware' => ['auth', 'verified', 'verify_host', 'user_block']], function () {
    Route::post('/get', [NotificationsController::class, 'getNotification'])->name('notification-get');
    Route::get('', [NotificationsController::class, 'index'])->name('notification-index');
    Route::post('/disable', [NotificationsController::class, 'disable'])->name('notification-disable');
    // Route::get('/edit/{id}', [CommentController::class, 'edit'])->name('comments-edit');
    // Route::get('/delete', [CommentController::class, 'delete'])->name('comments-delete');
});


// Rotas para manipulação de likes
Route::group(['prefix' => 'like', 'middleware' => ['auth', 'verified', 'verify_host', 'user_block']], function () {
    Route::post('/create', [LikeController::class, 'create'])->name('like-create');
    Route::delete('/delete', [LikeController::class, 'delete'])->name('like-delete');
});

// Rotas para manipulação de estrelas
Route::group(['prefix' => 'stars', 'middleware' => ['auth', 'verified', 'verify_host', 'user_block']], function () {
    Route::post('/create', [StarController::class, 'create'])->name('star-create');
    Route::delete('/delete', [StarController::class, 'delete'])->name('star-delete');
});

Route::get('logout', 'Auth\LoginController@logout')->name('logout');

/*Rotas de mods*/
// Rotas específicas para GTAV
Route::group(['prefix' => 'gtav'], function () {
    Route::get('', [GtavController::class, 'index'])->name('index-gtav');  
    Route::get('/{category}/{tag?}', [GtavController::class, 'search'])->name('search-category-gtav-and-tag');  
});

// Rotas específicas para GTAIV
Route::group(['prefix' => 'gtaiv'], function () {
    Route::get('', [GtaivController::class, 'index'])->name('index-gtaiv');  
    Route::get('/{category}/{tag?}', [GtaivController::class, 'search'])->name('search-category-gtaiv-and-tag');  
});

// Rotas específicas para GTASA
Route::group(['prefix' => 'gtasa'], function () {
    Route::get('', [GtasaController::class, 'index'])->name('index-gtasa');  
    Route::get('/{category}/{tag?}', [GtasaController::class, 'search'])->name('search-category-gtasa-and-tag');  
});

// Rotas específicas para ETS2
Route::group(['prefix' => 'ets2'], function () {
    Route::get('', [Ets2Controller::class, 'index'])->name('index-ets2');  
    Route::get('/{category}/{tag?}', [Ets2Controller::class, 'search'])->name('search-category-ets2-and-tag');
});

// Rotas específicas para Assetto Corsa
Route::group(['prefix' => 'assetocorsa'], function () {
    // Adicione suas rotas aqui
});

// Rotas específicas para modelos 3D
Route::group(['prefix' => 'models3d'], function () {
    Route::get('', [Models3dController::class, 'index'])->name('index-models3d');  
    Route::get('/{category}/{tag?}', [Models3dController::class, 'search'])->name('search-category-models3d-and-tag');
});
/*fim rotas de mods.*/

/*Rotas para tratamento de imagem*/
Route::get('resize/{resize}/mods/images/{args}', function($resize,$args){
    $resize = explode('-', $resize);

    $img = Image::cache(function($image) use($args, $resize){
            $file = Storage::disk('local')->get("/mods/images/{$args}");
            $img = $image->make($file)->resize($resize[0], $resize[1]);
        },24000, true );

    return Image::make($img)->response('jpg', $resize[2]);
})->name('resize-image');

Route::get('/images/{path}/{args}', function($path, $args){
    
    $img = Image::cache(function($image) use($args, $path){
            $file = Storage::disk('local')->get("images/$path/$args");
            $img = $image->make($file);
        },24000, true );

    return Image::make($img)->response('jpg', 80);
});

Route::get('/get/logo', function(){

    $logo = Storage::disk('local')->get("logo-img/logo.png");

    $logo = Image::make($logo)->resize(256, null, function ($constraint) { $constraint->aspectRatio(); } );

    return $logo->response('png', 70);
})->name('get-logo');

Route::get('/images/user/img/perfil/{args}', function ($args)
{
    $img = Image::cache(function($image) use($args){
            $file = Storage::disk('local')->get("user/img/perfil/$args");
            $img = $image->make($file)->resize(256, null, function ($constraint) { $constraint->aspectRatio(); } );
        },24000, true );

    return Image::make($img)->response('jpg', 60);
});


// Route::any('watermark', 'AdminController@waterMark')->name('water-mark');
Auth::routes(['verify' => true]);

Route::any('user/block', function(){
    return view('auth.verify-user-block');
})->name('user-block');
