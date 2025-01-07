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
        
        $restaurants = Restaurant::query()
                             ->when($keyword, function($query,$keyword) {
                                return $query->where('name', 'LIKE', "%{$keyword}%");
                             });
        $restaurants = Restaurant::paginate(15);
        $total = $restaurants->count();

        return view('admin.restaurants.index', compact('restaurants', 'keyword', 'total'));
    }

    // 店舗詳細ページ
    public function show(Restaurant $restaurant) {
        return view('admin.restaurants.show',compact('restaurant'));
    }

    // 店舗登録ページ
    public function create() {
        return view('admin.restaurants.create');
    }

    // 店舗登録機能
    public function store(Request $request) {
        
        // バリデーション
        $request->validate([
            'name' => 'required',
            'image' => 'image|max:2048',
            'description' => 'required',
            'lowest_price' => 'required|numeric|min:0|lte:highest_price',
            'highest_price' => 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|digits:7',
            'address' => 'required',
            'opening_time' => 'required|before:closing_time',
            'closing_time' => 'required|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0'
        ]);

        // 入力されたデータを保存する（画像がある場合は、storage/app/public/restaurantsに保存し、変数$imageを代入する）
        $restaurant = new Restaurant();
        $restaurant->name = $request->input('name');
        if ($request->hasfile('image')) {
            $image = $request->file('image')->store('public/restaurants');
            $restaurant->image = basename($image);
        } else {
            $restaurant->image = '';
        }
        $restaurant->description = $request->input('description');
        $restaurant->lowest_price = $request->input('lowest_price');
        $restaurant->highest_price = $request->input('high_price');
        $restaurant->postal_code = $request->input('postal_code');
        $restaurant->address = $request->input('address');
        $restaurant->opening_time = $request->input('opening_time');
        $restaurant->closing_time = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');
        $restaurant->save();

        return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を登録しました。');
    }

    // 店舗編集ページ
    public function edit(Restaurant $restaurant) {
        return view('admin.restaurants.edit', compact('restaurant'));
    }

    // 店舗更新機能
    public function update(Request $request, Restaurant $restaurant) {

        // バリデーション
        $request->validate([
            'name' => 'required',
            'image' => 'image|max:2048',
            'description' => 'required',
            'lowest_price' => 'required|numeric|min:0|lte:highest_price',
            'highest_price' => 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|digits:7',
            'address' => 'required',
            'opening_time' => 'required|before:closing_time',
            'closing_time' => 'required|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0'
        ]);

        // 入力されたデータを保存する（画像がある場合は、storage/app/public/restaurantsに保存し、変数$imageを代入する）
        $restaurant->name = $request->input('name');
        if ($request->hasfile('image')) {
            $image = $request->file('image')->store('public/restaurants');
            $restaurant->image = basename($image);
        } else {
            $restaurant->image = '';
        }
        $restaurant->description = $request->input('description');
        $restaurant->lowest_price = $request->input('lowest_price');
        $restaurant->highest_price = $request->input('highest_price');
        $restaurant->postal_code = $request->input('postal_code');
        $restaurant->address = $request->input('address');
        $restaurant->opening_time = $request->input('opening_time');
        $restaurant->closing_time = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');
        $restaurant->update();

        return redirect()->route('admin.restaurants.show', $restaurant)->with('flash_message', '店舗を編集しました。');

    }

    // 店舗削除機能

    public function destroy(Restaurant $restaurant) {

        $restaurant->delete();

        return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を削除しました。');
    }

}
