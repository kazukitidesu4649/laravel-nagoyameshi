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
use App\Http\Controllers\Admin\CompanyController as AdminCompanyController;
use App\Http\Controllers\Admin\TermController as AdminTermController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TermController;
use App\Models\Company;
use App\Models\Restaurant;
use Illuminate\Auth\Events\Verified;

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
    Route::get('/company', [CompanyController::class, 'index'])->name('company.index');
    Route::get('/terms', [TermController::class, 'index'])->name('terms.index');
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

    // レビュー管理
    Route::get('restaurants/{restaurant}/reviews', [ReviewController::class, 'index'])->name('restaurants.reviews.index');
    Route::post('restaurants/{restaurant}/reviews', [ReviewController::class, 'store'])->name('restaurants.reviews.store');
    Route::middleware('Subscribed')->resource('restaurants.reviews', ReviewController::class)->except(['index','store', 'show']);

    // 予約管理
    Route::middleware('Subscribed')->get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::middleware('Subscribed')->get('/restaurants/{restaurant}/reservations/create', [ReservationController::class, 'create'])->name('restaurants.reservations.create');
    Route::middleware('Subscribed')->post('/restaurants/{restaurant}/reservations', [ReservationController::class, 'store'])->name('restaurants.reservations.store');
    Route::middleware('Subscribed')->delete('/reservations/{reservations}', [ReservationController::class, 'destroy'])->name('reservations.destroy');

    // お気に入り機能
    Route::middleware(['auth', 'verified', 'Subscribed'])->group(function(){
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{restaurant}', [FavoriteController::class , 'store'])->name('favorites.store');
    Route::delete('/favorites/{restaurant}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    });
});

// 管理者用ルートグループ
Route::redirect('/admin', '/admin/login');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
        Route::get('home', [Admin\HomeController::class, 'index'])->name('home');
        Route::resource('users', Admin\UserController::class);
        Route::resource('restaurants', Admin\RestaurantController::class);
        Route::resource('categories', Admin\CategoryController::class);
        Route::resource('company', Admin\CompanyController::class);
        Route::resource('terms', Admin\TermController::class)->except('show');
});
