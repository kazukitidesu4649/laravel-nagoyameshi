<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    // createアクション(登録ページ)
    public function create() {
        $intent = Auth::user()->createSetupIntent();

        return view('subscription.create', compact('intent'));
    }

    // storeアクション（登録機能）
    public function store(Request $request) {

        $user = Auth::user();

        $paymentMethod = $request->input('payment_method');

        try {
            $user->newSubscription('premium_plan', 'price_1Qj9UuLrDOeQcDxNv4X1he93')
            ->create($paymentMethod);

            session()->flash('flash_message', '有料プランへの登録が完了しました。');
            return redirect()->route('home');
        } catch (\Exception $se) {
            return back()->withErrors(['error' => '登録処理中にエラーが発生しました:'. $e->getMessage()]);
        }
    }
}
