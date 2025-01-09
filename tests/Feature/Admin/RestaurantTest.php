<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use App\Http\Controllers\Admin\RestaurantController;
use Database\Factories\UserFactory;
use Database\Factories\AdminFactory;
use Database\Factories\RestaurantFactory;

class RestaurantTest extends TestCase
{
    // indexアクション（店舗一覧ページ）
    public function test_guest_cannot_access_admin_restaurant_index()
    {
        $response = $this->get(route('admin.restaurants.index'));

        $response->assertRedirect('admin/login');
    }

    public function test_user_cannot_access_admin_restaurant_index()
    {
        $user = UserFactory::new()->create();
        $response = $this->actingAs($user)->get(route('admin.restaurants.index'));

        $response->assertRedirect('admin/login');
    }

    public function test_admin_can_access_admin_restaurant_index()
    {
        $admin = AdminFactory::new()->create();
        $response = $this->actingAs($admin,'admin')->get(route('admin.restaurants.index'));

        $response->assertStatus(200);
    }

    // showアクション（店舗詳細ページ）
    public function test_guest_cannot_access_admin_restaurant_show()
    {   
        $response = $this->get('/admin/restaurants/4');

        $response->assertRedirect('admin/login');
    }

    public function test_user_cannot_access_admin_restaurant_show()
    {
        $user = UserFactory::new()->create();
        $response = $this->actingAs($user)->get('/admin/restaurants/4');

        $response->assertRedirect('admin/login');
    }

    public function test_admin_can_access_admin_restaurant_show()
    {
        $admin = AdminFactory::new()->create();
        $response = $this->actingAs($admin,'admin')->get('/admin/restaurants/4');

        $response->assertStatus(200);
    }

    // createアクション（店舗登録ページ）
    public function test_guest_cannot_access_admin_restaurant_create()
    {   
        $response = $this->get('/admin/restaurants/create');

        $response->assertRedirect('admin/login');
    }

    public function test_user_cannot_access_admin_restaurant_create()
    {
        $user = UserFactory::new()->create();
        $response = $this->actingAs($user)->get(route('admin.restaurants.create'));

        $response->assertRedirect('admin/login');
    }

    public function test_admin_can_access_admin_restaurant_create()
    {
        $admin = AdminFactory::new()->create();
        $response = $this->actingAs($admin,'admin')->get(route('admin.restaurants.create'));

        $response->assertStatus(200);
    }

    // storeアクション（店舗登録機能）
    public function test_guest_cannot_store_admin_restaurant()
    {   
        $restaurantData = RestaurantFactory::new()->create()->toArray();
        
        $response = $this->withoutMiddleware()->post(route('admin.restaurants.store'), $restaurantData);

        $response->assertRedirect(route('admin.restaurants.index'));
    }

    public function test_user_cannot_store_admin_restaurant()
    {
        $restaurantData = RestaurantFactory::new()->create()->toArray();
        $user = UserFactory::new()->create();
        $response = $this->withoutMiddleware()->post(route('admin.restaurants.store'), $restaurantData);

        $response->assertRedirect(route('admin.restaurants.index'));
    }

    public function test_admin_can_access_store_admin_restaurant()
    {
        $restaurantData = RestaurantFactory::new()->create()->toArray();
        $admin = AdminFactory::new()->create();
        $response = $this->actingAs($admin, 'admin')->withoutMiddleware()->post(route('admin.restaurants.store'), $restaurantData);

        $response->assertRedirect(route('admin.restaurants.index'));
    }
}
