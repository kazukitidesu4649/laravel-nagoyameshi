<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    // indexアクション（カテゴリ一覧ページ）
    public function index (Request $request) {
        $keyword = $request->input('keyword');

        if($keyword) {
            $categories = Category::where('id', 'name', "%{$keyword}%");
        } else {
            $categories = Category::paginate(15);
        }

        $total = $categories->total();

        return view('admin.categories.index', compact('categories', 'keyword', 'total'));
    }
}
