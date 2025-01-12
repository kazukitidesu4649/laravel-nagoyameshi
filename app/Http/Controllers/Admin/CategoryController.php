<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    // indexアクション（カテゴリ一覧ページ）
    public function index (Request $request) {
        $keyword = $request->input('keyword');

        if($keyword !== null) {
            $categories = Category::where('name', 'LIKE', "%{$keyword}%")
            ->paginate(15);
            $total = $categories->total();
        } else {
            $categories = Category::paginate(15);
            $total = 0;
            $keyword = null;
        }
        return view('admin.categories.index', compact('categories', 'keyword', 'total'));
    }

    // storeアクション（カテゴリ登録機能）
    public function store(Request $request){
        
        $varidated = $request->validate([
            'name' => 'required'
        ]);

        Category::create($varidated);

        return redirect(route('admin.categories.index'))->with('flash_message', 'カテゴリを登録しました。');
    }

    // updateアクション（カテゴリ更新機能）
    public function update(Request $request, Category $category){
        
        $varidated = $request->validate([
            'name' => 'required'
        ]);

        $category->update($varidated);

        return redirect(route('admin.categories.index'))->with('flash_message', 'カテゴリを編集しました。');
    }

    // destroyアクション（カテゴリ削除機能）
    public function destroy(Category $category) {

        $category->delete();

        return redirect(route('admin.categories.index'))->with('flash_message', 'カテゴリを削除しました。');
    }
}
