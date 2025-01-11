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

        if($keyword) {
            $categories = Category::where('id', 'name', "%{$keyword}%");
        } else {
            $categories = Category::paginate(15);
        }

        $total = $categories->total();

        return view('admin.categories.index', compact('categories', 'keyword', 'total'));
    }

    // storeアクション（カテゴリ登録機能）
    public function store(Request $request){
        
        $varidated = $request->validate([
            'name' => 'required'
        ]);

        $category = new Category($varidated);
        $category->save();

        return redirect(route('admin.categories.index'))->with('flash_message', 'カテゴリを登録しました。');
    }

    // updateアクション（カテゴリ更新機能）
    public function update(Request $request, Category $category){
        
        $varidated = $request->validate([
            'name' => 'required'
        ]);

        $category->update($request->all());

        return redirect(route('admin.categories.index'))->with('flash_message', 'カテゴリを編集しました。');
    }

    // destroyアクション（カテゴリ削除機能）
    public function destroy(Category $category) {

        $category->delete();

        return redirect(route('admin.categories.index'))->with('flash_message', 'カテゴリを削除しました。');
    }
}
