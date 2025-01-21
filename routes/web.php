<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RestaurantController as AdminRestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TermController;
use App\Http\Controllers\SubscriptionController;
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
// 認証とメール認証が必要なルート
Route::group(['middleware' => ['auth', 'verified']], function(){
    Route::prefix('user')->name('user.')->group(function() {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::patch('{user}', [UserController::class, 'update'])->name('update');
    });

    // サブスクリプション管理
    Route::middleware('NotSubscribed')->group(function(){
        Route::get('subscription/create', [SubscriptionController::class, 'create'])->name('subscription.create');
        Route::post('subscription/store', [SubscriptionController::class, 'store'])->name('subscription.store');
    });

    Route::middleware('Subscribed')->group(function () {
        Route::prefix('subscription')->name('subscription.')->group(function(){
            Route::get('edit', [SubscriptionController::class, 'edit'])->name('edit');
            Route::patch('update', [SubscriptionController::class, 'update'])->name('update');
            Route::get('cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
            Route::delete('/', [SubscriptionController::class, 'destroy'])->name('destroy');
        });
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
