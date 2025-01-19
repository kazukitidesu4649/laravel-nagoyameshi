<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index() {
       
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.home');
        }

        $user = Auth::user();

        return view('user.index', compact('user'));
    }

    public function edit(User $user)
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.home');
        }

        if(Auth::id() !== $user->id)
        {
            return redirect()->route('user.index')->with('error_message', '不正なアクセスです。');
        }
        return view('user.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {

        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.home');
        }

    if (Auth::id() !== $user->id) {
        return redirect()->route('user.index')->with('error_message', '不正なアクセスです。');
    }

    // バリデーションが通ったデータを取得
    $validated = $request->validated();

    // ユーザーの更新
    $user->update($validated);

    // フラッシュメッセージの追加
    $request->session()->flash('flash_message', '会員情報を編集しました。');

    // ユーザー一覧ページにリダイレクト
    return redirect()->route('user.index');
    }
}
