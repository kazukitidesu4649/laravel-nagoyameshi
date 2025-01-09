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
}
