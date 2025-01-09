<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Http\Controllers\Admin\RestaurantController;
use Database\Factories\UserFactory;
use Database\Factories\AdminFactory;
use Database\Factories\RestaurantFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

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

    // editアクション（店舗編集ページ）
    public function test_guest_cannot_access_admin_restaurant_edit()
    {   
        $response = $this->get('/admin/restaurants/1/edit');

        $response->assertRedirect('admin/login');
    }

    public function test_user_cannot_access_admin_restaurant_edit()
    {
        $user = UserFactory::new()->create();
        $restaurant = RestaurantFactory::new()->create()->toArray();
        $response = $this->actingAs($user)->get('admin/restaurants/1/edit');

        $response->assertRedirect('admin/login');
    }

    public function test_admin_can_access_admin_restaurant_edit()
    {
        $admin = AdminFactory::new()->create();
        $restaurant = RestaurantFactory::new()->create()->toArray();
        $response = $this->actingAs($admin,'admin')->get('admin/restaurants/1/edit');

        $response->assertStatus(200);
    }

    // updateアクション（店舗更新機能）
    public function test_guest_cannot_update_admin_restaurant()
    {   
    $restaurant = RestaurantFactory::new()->create();

    $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
                     ->put('/admin/restaurants/' . $restaurant->id, [
                         'name' => 'New Restaurant',
                     ]);

    $response->assertRedirect(route('admin.login'));
    }

    public function test_user_cannot_update_admin_restaurant()
    {
        $user = UserFactory::new()->create();
        $restaurant = RestaurantFactory::new()->create();

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
                     ->actingAs($user)
                     ->put('/admin/restaurants/' . $restaurant->id, [
                         'name' => 'New Restaurant',
                     ]);

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_update_admin_restaurant()
    {
        $admin = AdminFactory::new()->create();
        $restaurant = RestaurantFactory::new()->create();

        $restaurant_updata = [
            'name' => 'name',
            'description' => 'name is name',
            'lowest_price' => '1',
            'highest_price' => '10',
            'postal_code' => '1234567',
            'address' => '123 main 12',
            'opening_time' => '12:00',
            'closing_time' => '22:00',
            'seating_capacity' => '22',
        ];
        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
        ->actingAs($admin, 'admin')->put('/admin/restaurants/'. $restaurant->id, $restaurant_updata);

        $response->assertRedirect('/admin/restaurants/' .$restaurant->id);
    }

    // destroyアクション（店舗削除機能）
    public function test_guest_cannot_destroy_admin_restaurant()
    {   
        $restaurant = RestaurantFactory::new()->create();

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
                     ->delete('/admin/restaurants/' . $restaurant->id, );

        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_cannot_destroy_admin_restaurant()
    {   
        $user = UserFactory::new()->create();
        $restaurant = RestaurantFactory::new()->create();
        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
                    ->actingAs($user,'web') 
                    ->delete('/admin/restaurants/' . $restaurant->id, );

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_destroy_admin_restaurant()
    {
        $admin = AdminFactory::new()->create();
        $restaurant = RestaurantFactory::new()->create();
        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
        ->actingAs($admin, 'admin')->delete('/admin/restaurants/'. $restaurant->id);

        $response->assertRedirect(route('admin.restaurants.index'));
    }
}
