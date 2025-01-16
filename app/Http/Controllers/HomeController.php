<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;

class HomeController extends Controller
{
    // indexアクション

public function index() {

    $highly_rated_restaurants = Restaurant::get();
    $categories = Category::all();
    $new_restaurants = Restaurant::orderBy('created_at', 'desc')->take(6)->get();

    return view('home', [
        'highly_rated_restaurant' => $highly_rated_restaurants,
        'categories' => $categories,
        'new_restaurant' => $new_restaurants,
    ]);
}
}
