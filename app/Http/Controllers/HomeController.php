<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;

class HomeController extends Controller
{
    // indexアクション

    public function index() {

        $highly_rated_restaurants = Restaurant::take(6)->get();
        $categories = Category::all();
        $new_restaurants = Restaurant::orderBy('created_at', 'desc')->take(6)->get();

        return view('home', [
            'highly_rated_restaurants' => $highly_rated_restaurants,
            'categories' => $categories,
            'new_restaurants' => $new_restaurants,
        ]);
    }
}
