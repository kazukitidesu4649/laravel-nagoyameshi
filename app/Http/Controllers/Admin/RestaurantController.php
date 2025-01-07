<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;

class RestaurantController extends Controller
{
    // 店舗一覧ページ
    public function index(Request $request){

        $keyword = $request->input('keyword');
        
        $query = Restaurant::query()
                             ->when($keyword, function($query,$keyword) {
                                return $query->where('name', 'LIKE', "%{$keyword}%");
                             });

        $total = $query->count();

        return view('admin.restaurants.index', compact('restaurants', 'keyword', 'total'));
    }

    // 店舗詳細ページ
    public function show(Restaurant $restaurant) {
        return view('admin.restaurants.show',compact('restaurants'));
    }
}
