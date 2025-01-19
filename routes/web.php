<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RestaurantController as AdminRestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TermController;
use App\Models\Restaurant;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

require __DIR__.'/auth.php';

Route::group(['middleware' => 'guest:admin'], function() {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
    Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show'])->name('restaurants.show');
});

Route::group(['middleware' => ['auth', 'verified']], function(){
    Route::prefix('user')->name('user.')->group(function() {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::patch('{user}', [UserController::class, 'update'])->name('update');
    });
});

// 管理者用ルートグループ
Route::redirect('/admin', '/admin/login');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
        Route::get('home', [Admin\HomeController::class, 'index'])->name('home');
        Route::resource('users', UserController::class);
        Route::resource('restaurants', Admin\RestaurantController::class);
        Route::resource('categories', Admin\CategoryController::class);
        Route::resource('company', Admin\CompanyController::class);
        Route::resource('terms', Admin\TermController::class)->except('show');
});