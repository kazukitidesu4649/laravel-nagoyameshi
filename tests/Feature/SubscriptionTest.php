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

    // createアクション（有料プラン登録ページ）4検証
    public function test_guest_cannot_access_create_subscrimeb()
    {
        $response = $this->get('subscription/create');

        $response->assertRedirect('/login');
    }

    public function test_user_can_access_create_subscrimeb()
    {
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user)->get('subscription/create');
        $response->assertStatus(200);
    }

    // public function test_subscrimeb_user_cannot_access_create_subscrimeb()
    // {
    //     $user = UserFactory::new()->create();
    //     $user->newSubscription('premium_plan', 'price_1Qj9UuLrDOeQcDxNv4X1he93')->create('pm_card_visa');

    //     $response = $this->actingAs($user)->get('subscription/create');

    //     $response->assertRedirect('/');
    //     $response->assertSessionHas('flash_message', 'すでに有料会員です。');
    // }

    public function test_admin_cannot_access_create_subscrimeb()
    {
        $admin = AdminFactory::new()->create();
        $response = $this->actingAs($admin, 'admin')->get('subscription/create');
        $response->assertRedirect('admin/home');
    }

    // storeアクション（有料プラン登録機能）
    public function test_guest_cannot_store_subscrimeb()
    {
        $response = $this->post('subscription/store', [
            'paymentMethodId' => 'pm_card_visa'
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_store_subscrimeb()
    {
        $user = UserFactory::new()->create();
        $response = $this->actingAs($user)->post('subscription/store', [
            'payment_method' => 'pm_card_mastercard' , 
        ]);

        $response->assertRedirect(route('home'));
        $user->refresh();
        $this->assertTrue($user->subscribed('premium_plan'));
    }

    public function test_subscrimeb_user_cannot_store_subscrimeb()
    {
        $user = UserFactory::new()-> create();
        $user->newSubscription('premium_plan', 'price_1Qj9UuLrDOeQcDxNv4X1he93')->create('pm_card_visa');

        $response = $this->actingAs($user)->post('subscription/store', [
            'payment_method' => 'pm_card_mastercard' , 
        ]);

        $response->assertRedirect('/');
        $this->assertTrue($user->fresh()->subscribed('premium_plan'));
    }

    public function test_admin_subscrimeb_user_cannot_store_subscrimeb()
    {
            $admin = AdminFactory::new()->create();
            $response = $this->actingAs($admin, 'admin')->post('subscription/store', [
                'paymentMethodId' => 'pm_card_visa'
            ]);
    
            $response->assertRedirect(route('admin.home'));
    }
}
