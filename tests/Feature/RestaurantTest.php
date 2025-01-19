<?php

namespace Tests\Feature;

use Database\Factories\AdminFactory;
use Database\Factories\RestaurantFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    // 店舗一覧ページを作ろうのテスト
    public function test_guest_can_access_restaurant_index()
    {
        $restaurant = RestaurantFactory::new()->create();

        $response = $this->get(route('restaurants.index'));
        $response->assertStatus(200);
        $response->assertViewIs('restaurants.index');
    }

    public function test_user_can_access_restaurant_index()
    {
        $restaurant = RestaurantFactory::new()->create();
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user)->get(route('restaurants.index'));
        $response->assertStatus(200);
        $response->assertViewIs('restaurants.index');
    }

    public function test_admin_cannot_access_restaurant_index()
    {
        $restaurant = RestaurantFactory::new()->create();
        $admin = AdminFactory::new()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.index'));
        $response->assertRedirect(route('admin.home'));
    }
}
