<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class ReviewController extends Controller
{
    public function __construct()
    {
        if (auth('admin')->check()) {
            return redirect()->route('admin.home');
        }
    }

    // indexアクション（レビュー一覧ページ）
    public function index(Restaurant $restaurant) {
        $reviews = $restaurant->reviews()->orderBy('created_at', 'desc');
        $user = Auth::user();

        if($user && $user->subscribed('premium_plan')){
            $reviews = $reviews->paginate(5);
        } else {
            $reviews = $reviews->paginate(3);
        }

        return view('reviews.index', compact('restaurant', 'reviews'));
    }

    // createアクション（レビュー作成ページ）
    public function create(Restaurant $restaurant){
        return view('reviews.create', compact('restaurant'));
    }

    // storeアクション（レビュー作成機能）
    public function store(Request $request, Restaurant $restaurant, Review $review) {

        if (auth('admin')->check()) {
            return redirect()->route('admin.home');
        }
        
        $request->validate(([
            'score' => 'required|numeric|between:1,5',
            'content' => 'required',
        ]));

        $review = new Review([
            'score' => $request->score,
            'content' => $request->content,
            'restaurant_id' => $restaurant->id,
            'user_id' => auth()->id(),
        ]);

        $review->save();

        return redirect()->route('restaurants.reviews.index', ['restaurant' => $restaurant->id])
            ->with('flash_message', 'レビューを投稿しました。');
    }

    // editアクション（レビュー編集ページ）
    public function edit(Restaurant $restaurant, Review $review){
        if($review->user_id !== auth()->id()){
            return redirect()->route('restaurants.reviews.index', ['restaurant' => $restaurant])
                ->with('error_message', '不正なアクセスです.');
        }
        // ユーザーがアクセスできる場合、編集フォームを表示する
        return view('reviews.edit', compact('restaurant', 'review'));
    }

    // updateアクション（レビュー更新機能）
    public function update(Request $request, Restaurant $restaurant, Review $review){
        if ($review->user->id !== auth()->id()){
            return redirect()->route('restaurants.reviews.index', ['restaurants' => $restaurant ])
            ->with('error_message', '不正なアクセスです。');
        }

        $request->validate([
            'score' => 'required|numeric|between:1,5',
            'content' => 'required',
        ]);

        $review->update($request->only(['score', 'content']));
        
        return redirect(route('restaurants.reviews.index', $restaurant))
            ->with('flash_message', 'レビューを編集しました。');
    }

    // destroyアクション（レビュー削除）
    public function destroy(Request $request, Restaurant $restaurant, Review $review) {
        if($review->user_id !== auth()->id()) {
            return redirect()->route('restaurants.reviews.index', ['restaurant' => $restaurant])
            ->with('error_message', '不正なアクセスです。');
        }

        $review->delete();
        return redirect(route('restaurants.reviews.index', $restaurant))->with('flash_message', 'レビューを削除しました。');
    }
}
