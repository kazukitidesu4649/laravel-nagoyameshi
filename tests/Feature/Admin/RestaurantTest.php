<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Http\Controllers\Admin\RestaurantController;
use App\Models\RegularHoliday;
use Database\Factories\UserFactory;
use Database\Factories\AdminFactory;
use Database\Factories\RestaurantFactory;
use Database\Factories\CategoryFactory;
use Database\Factories\RegularHolidayFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RestaurantTest extends TestCase
{   
    use RefreshDatabase;

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
        $restaurant = RestaurantFactory::new()->create();
        $response = $this->actingAs($admin,'admin')->get(route('admin.restaurants.show', ['restaurant' => $restaurant->id]));

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
        $categories = CategoryFactory::new()->count(3)->create();

        $restaurantData = RestaurantFactory::new()->create()->toArray();
        $restaurantData['category_ids'] = $categories->pluck('id')->toArray();
        $restaurantData['regular_holiday_ids'] = RegularHolidayFactory::new()->count(1)->create()->pluck('id')->toArray();
        
        $response = $this->post(route('admin.restaurants.store'), $restaurantData);
        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_cannot_store_admin_restaurant()
    {
        $categories = CategoryFactory::new()->count(3)->create();

        $user = UserFactory::new()->create();
        $restaurantData = RestaurantFactory::new()->create()->toArray();
        $restaurantData['category_ids'] = $categories->pluck('id')->toArray();
        $restaurantData['regular_holiday_ids'] = RegularHolidayFactory::new()->count(1)->create()->pluck('id')->toArray();

        $response = $this->actingAs($user)->post(route('admin.restaurants.store'), $restaurantData);

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_store_restaurant()
    {
        $admin = AdminFactory::new()->create();
        $this->actingAs($admin, 'admin');

        $categories = CategoryFactory::new()->count(3)->create();
        $restaurantData['category_ids'] = $categories->pluck('id')->toArray();
        $restaurantData['regular_holiday_ids'] = RegularHolidayFactory::new()->count(1)->create()->pluck('id')->toArray();

        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)->post('/admin/restaurants', $restaurantData);
        $response->assertRedirect('/admin/restaurants');

        $this->assertDatabaseHas('restaurants', [
            'name' => $restaurantData['name'],
            'description' => $restaurantData['description'],
            'lowest_price' => $restaurantData['lowest_price'],
            'higheset_price' => $restaurantData['higheset_price'],
            'postal_code' => $restaurantData['postal_code'],
            'address' => $restaurantData['address'],
            'opening_time' => $restaurantData['opening_time'],
            'closing_time' => $restaurantData['closing_time'],
            'seating_capacity' => $restaurantData['seating_capacity'],
        ]);

        $restaurant = Restaurant::create($restaurantData);

        $restaurant->categories()->attach($categories->pluck('id')->toArray());

        foreach($categories as $category) {
            $this->assertDatabaseHas('category_restaurant', [
                'restaurant_id' => $restaurant->id,
                'category_id' => $category->id
            ]);
        }
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

    public function admin_can_access_admin_restaurant_edit()
    {
        $admin = AdminFactory::new()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.edit', $restaurant));
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
    $categories = CategoryFactory::new()->count(3)->create();
    $category_ids = $categories->pluck('id')->toArray();

    $restaurant_updata = [
        'name' => 'Updated Restaurant Name',
        'description' => 'Updated Description',
        'lowest_price' => '100',
        'highest_price' => '1000',
        'postal_code' => '1234567',
        'address' => 'Updated Address',
        'opening_time' => '10:00',
        'closing_time' => '22:00',
        'seating_capacity' => '50',
        'category_ids' => $category_ids
    ];

    // 管理者としてPUTリクエスト
    $response = $this->actingAs($admin, 'admin')
        ->put(route('admin.restaurants.update', $restaurant->id), $restaurant_updata);

    // レスポンスが正しくリダイレクトされているか確認
    $response->assertRedirect(route('admin.restaurants.show', $restaurant->id));

    // デバッグ用（必要に応じて有効化）
    // dd($response->
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
