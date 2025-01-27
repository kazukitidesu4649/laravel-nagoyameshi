<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;


class FavoriteController extends Controller
{
    public function __construct()
    {
        if (auth('admin')->check()) {
            return redirect()->route('admin.home');
        }
    }

    // indexアクション（お気に入りページ）
    public function index(Request $request) {
        
        $favorite_restaurants = auth()->user()
            ->favorite_restaurants()
            ->orderBy('restaurant_user.created_at', 'desc')
            ->Paginate(15);

            return view('favorites.index', compact('favorite_restaurants'));
    }

    // storeアクション（お気に入り追加機能）
    public function store(Request $request, Restaurant $restaurant, User $user) {

        if (auth('admin')->check('admin')) {
            return redirect()->route('admin.home');
        }

        $user = auth()->user();
        $user->favorite_restaurants()->attach($restaurant->id);

        return redirect()->back()->with('flash_message', 'お気に入りに追加しました。');
    }

    // destroyアクション（お気に入り解除機能）
    public function destroy(Restaurant $restaurant){

        $user = auth()->user();
        $user->favorite_restaurants()->detach($restaurant->id);

        return redirect()->back()->with('flash_message', 'お気に入りを解除しました。');
    }
}
