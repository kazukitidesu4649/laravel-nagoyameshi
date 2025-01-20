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

    // editアクション（お支払い方法編集）
    public function edit(){
        $user = Auth::user();

        $intent = $user->createSetupIntent();

        return view('subscription.edit', [
            'user' => $user,
            'intent' => $intent,
        ]);
    }

    // updateアクション（お支払い方法更新）
    public function update(Request $request){
        $user = Auth::user();
        
        try {
            $user->updateDefaultPaymentMethod($request->paymentMethodId);

            return redirect('/')
                ->with('flash_message', 'お支払い方法を変更しました。');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'お支払いの方法の更新に失敗しました:' . $e->getMessage()]);
        }
    }

    // cancelアクション（有料プラン解約ページ）
    public function cancel() {
        $user = Auth::user();

        return view('subscription.cancel', compact('user'));
    }

    // destroyアクション（有料プラン解約機能）
    public function destroy() {
        $user = Auth::user();

        try {
            $user->subscription('premium_plan')->cancelNow();

            return redirect()->route('home')
                    ->with('flash_message', '有料プランを解約しました。');
        } catch (\Exception $e) {
            return back()
                    ->withErrors(['error' => '解約処理に失敗しました:' . $e->getMessage()]);
        }
    }
}
