<?php

namespace Tests\Feature;

use App\Services\StripeService;
use Database\Factories\UserFactory;
use Database\Factories\AdminFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Stripe\Stripe;
use Mockery;
use Stripe\SetupIntent;
use Stripe\StripeClient;
use Psy\Readline\Hoa\EventListens;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    // // createアクション（有料プラン登録ページ）4検証
    // public function test_guest_cannot_access_create_subscrimeb()
    // {
    //     $response = $this->get('subscription/create');

    //     $response->assertRedirect('/login');
    // }

    // public function test_user_can_access_create_subscrimeb()
    // {
    //     $user = UserFactory::new()->create();

    //     $response = $this->actingAs($user)->get('subscription/create');
    //     $response->assertStatus(200);
    // }

    public function test_subscrimeb_user_cannot_access_create_subscrimeb()
    {
        $user = UserFactory::new()->create();
        $user->newSubscription('premium_plan', 'price_1Qj9UuLrDOeQcDxNv4X1he93')->create('pm_card_visa');

        $response = $this->actingAs($user)->get('subscription/create');

        $response->assertRedirect('/');
        $response->assertSessionHas('flash_message', 'すでに有料会員です。');
    }

    // public function test_admin_cannot_access_create_subscrimeb()
    // {
    //     $admin = AdminFactory::new()->create();
    //     $response = $this->actingAs($admin, 'admin')->get('subscription/create');
    //     $response->assertRedirect('admin/home');
    // }

    // // storeアクション（有料プラン登録機能）
    // public function test_guest_cannot_store_subscrimeb()
    // {
    //     $response = $this->post('subscription/store', [
    //         'payment_method' => 'pm_card_visa', 
    //     ]);

    //     $response->assertRedirect(route('login'));
    // }

    // public function test_user_can_store_subscrimeb()
    // {
    //     $user = UserFactory::new()->create();
    //     $paymentMethodID = 'pm_card_visa';
        
    //     $response = $this->actingAs($user)->post('subscription/store', [
    //         'paymentMethodId' => $paymentMethodID, 
    //     ]);

    //     $response->assertRedirect(route('home'));
    //     $user->refresh();
    //     $this->assertTrue($user->subscribed('premium_plan'));
    // }

    // public function test_subscrimeb_user_cannot_store_subscrimeb()
    // {
    //     $user = UserFactory::new()-> create();
    //     $user->newSubscription('premium_plan', 'price_1Qj9UuLrDOeQcDxNv4X1he93')->create('pm_card_visa');

    //     $response = $this->actingAs($user)->post('subscription/store', [
    //         'payment_method' => 'pm_card_mastercard' , 
    //     ]);

    //     $response->assertRedirect('/');
    //     $this->assertTrue($user->fresh()->subscribed('premium_plan'));
    // }

    // public function test_admin_subscrimeb_user_cannot_store_subscrimeb()
    // {
    //         $admin = AdminFactory::new()->create();
    //         $response = $this->actingAs($admin, 'admin')->post('subscription/store', [
    //             'paymentMethodId' => 'pm_card_visa'
    //         ]);
    
    //         $response->assertRedirect(route('admin.home'));
    // }

    // // editアクション(お支払い方法編集ページ)
    // public function test_guest_cannot_access_subscription_edit()
    // {
    //     $response = $this->get(route('subscription.edit'));
    //     $response->assertRedirect('/login');
    // }

    // public function test_user_cannot_access_subscription_edit()
    // {
    //     $user = UserFactory::new()->create();
        
    //     $response = $this->actingAs($user)->get('/subscription/edit');
    //     $response->assertRedirect('/subscription/create');
    // }

    // public function test_subscription_user_can_access_subscription_edit()
    // {
    //     $user = UserFactory::new()->create();
    //     $user->newSubscription('premium_plan', 'price_1Qj9UuLrDOeQcDxNv4X1he93')->create('pm_card_visa');
    //     $response = $this->actingAs($user)->get('/subscription/edit');

    //     $response->assertStatus(200);
    //     $response->assertViewIs('subscription.edit');
    // }

    // public function test_admin_cannot_access_subscription_edit()
    // {
    //     $admin = AdminFactory::new()->create();
    //     $response = $this->actingAs($admin, 'admin')->get('/subscription/edit');
        
    //     $response->assertRedirect(route('admin.home'));
    // }

    // public function test_guest_cannot_update_subsc()
    // {
    //     $response = $this->patch(route('subscription.update'), [
    //         'paymentMethodId' => 'pm_card_mastercard',
    //     ]);

    //     $response->assertRedirect(route('login'));
    // }

    // public function test_user_cannot_update_subsc()
    // {
    //     $user = UserFactory::new()->create();
    //     $response = $this->actingAs($user)->patch(route('subscription.update'), [
    //         'paymentMethodId' => 'pm_card_mastercard',
    //     ]);

    //     $response->assertRedirect(route('subscription.create'));
    // }

    // public function test_subsc_user_can_update_subsc()
    // {
    //     $user = UserFactory::new()->create();
    //     $user->newSubscription('premium_plan', 'price_1Qj9UuLrDOeQcDxNv4X1he93')->create('pm_card_visa');

    //     $original_payment_method_id = $user->defaultPaymentMethod()->id;

    //     $response = $this->actingAs($user)->patch('/subscription/update', [
    //         'paymentMethodId' => 'pm_card_mastercard',
    //     ]);

    //     $response->assertRedirect(route('home'));
    //     $user->refresh();

    //     $this->assertNotEquals($original_payment_method_id, $user->defaultPaymentMethod()->id);
    // }

    // public function test_admin_cannot_update_subsc()
    // {
    //     $admin = AdminFactory::new()->create();

    //     $response = $this->actingAs($admin, 'admin')->patch('/subscription/update', [
    //         'paymentMethodId' => 'pm_card_mastercard',
    //     ]);

    //     $response->assertRedirect(route('admin.home'));
    // }

    // public function test_guest_cannot_access_cancel_page()
    // {
    //     $response = $this->get('/subscription/cancel');
    //     $response->assertRedirect('/login');
    // }

    // public function test_free_user_cannot_access_cancel_page()
    // {
    //     $user = UserFactory::new()->create();
    //     $response = $this->actingAs($user)->get('/subscription/cancel');
    //     $response->assertRedirect('/subscription/create');
    // }

    // public function test_paid_user_can_access_cancel_page()
    // {
    //     $user = UserFactory::new()->create();
    //     $user->newSubscription('premium_plan', 'price_1Qj9UuLrDOeQcDxNv4X1he93')->create('pm_card_visa');
    //     $response = $this->actingAs($user)->get('/subscription/cancel');

    //     $response->assertStatus(200);
    //     $response->assertViewIs('subscription.cancel');
    // }

//     public function test_admin_cannot_access_cancel_page()
//     {
//         $admin = AdminFactory::new()->create();
//         $response = $this->actingAs($admin, 'admin')->get('/subscription/cancel');

//         $response->assertRedirect(route('admin.home'));
//     }

//     public function test_guest_cannot_cancel_subscription()
//     {
//         $response = $this->delete('/subscription');
//         $response->assertRedirect('/login');
//     }

//     public function test_free_user_cannot_cancel_subscription()
//     {
//         $user = UserFactory::new()->create();
//         $response = $this->actingAs($user)->delete('/subscription');
//         $response->assertRedirect('/subscription/create');
//     }

    // public function test_paid_user_can_cancel_subscription()
    // {
    //     $user = UserFactory::new()->create();
    //     $user->newSubscription('premium_plan', 'price_1Qj9UuLrDOeQcDxNv4X1he93')->create('pm_card_visa');

    //     $response = $this->actingAs($user)->delete('/subscription');
    //     $response->assertRedirect(route('home'));
    //     $this->assertFalse($user->fresh()->subscribed('premium_plan'));
    // }

//     public function test_admin_cannot_cancel_subscription()
//     {
//         $admin = AdminFactory::new()->create();

//         $response = $this->actingAs($admin, 'admin')->delete('/subscription');
//         $response->assertRedirect(route('admin.home'));
//     }
}
