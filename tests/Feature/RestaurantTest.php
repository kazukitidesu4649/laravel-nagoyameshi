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

    // indexアクションのテスト
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

    // showアクションのテスト
    public function test_guest_can_access_restaurant_show()
    {
        $restaurant = RestaurantFactory::new()->create();

        $response = $this->get(route('restaurants.show', $restaurant->id));
        $response->assertStatus(200);
        $response->assertViewIs('restaurants.show');
    }

    public function test_guest_user_access_restaurant_show()
    {
        $user = UserFactory::new()->create();
        $restaurant = RestaurantFactory::new()->create();

        $response = $this->actingAs($user)->get(route('restaurants.show', $restaurant->id));
        $response->assertStatus(200);
        $response->assertViewIs('restaurants.show');
    }

    public function test_guest_admin_access_restaurant_show()
    {
        $admin = AdminFactory::new()->create();
        $restaurant = RestaurantFactory::new()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.show', $restaurant->id));
        $response->assertRedirect(route('admin.home'));
    }
}
