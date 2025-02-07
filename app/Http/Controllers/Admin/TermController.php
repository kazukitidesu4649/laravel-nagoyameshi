<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    // indexアクション（利用規約ページ）
    public function index() {
        
        $term = Term::first();

        return view('admin/terms/index', compact('term'));
    }

    // editアクション（利用規約編集ページ）
    public function edit(Term $term) {

        return view('admin/terms/edit', compact('term'));
    }

    // updateアクション（利用規約更新機能）
    public function update(Request $request, Term $term) {

        $request->validate(['content' => 'required']);
        $term->content = $request->content;
        $term->save();

        return redirect('admin/terms')
                ->with('flash_message', '利用規約を編集しました。');
    }
}
