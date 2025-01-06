<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request) {
        //検索ワードを取得
        $keyword = $request->input('keyword');
        
        // クエリの作成
        $query = User::query()->when($keyword, function ($query, $keyword){
                $query->where('name', 'LIKE', "%{$keyword}%")
                        ->orwhere('kana', 'LIKE',"%{$keyword}%");
            });
        // ページネーションでデータを取得
        $users = $query->paginate(15);
        //総数の計算
        $total = $query->count();
        
        //ビューにデータを渡す
        return view('users.index', compact('users', 'keyword', 'total'));    
    }

    public function show(User $user) {
        return view('Users.show', compact('user'));
    }
}
