<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Http\Controllers\FavoriteController;
use Database\Factories\RestaurantFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（お気に入り一覧ページ）
    public function test_guest_cannot_access_favorite_index()
    {
        $response = $this->get(route('favorites.index'));
        $response->assertRedirect('login');
    }

    public function test_user_cannot_access_favorite_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('favorites.index'));
        $response->assertRedirect(route('subscription.create'));
    }

    public function test_subsc_user_cannot_access_favorite_index()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Qj9UuLrDOeQcDxNv4X1he93')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('favorites.index'));
        $response->assertStatus(200);
    }

    public function test_guest_cannot_favorite_destroy()
    {   
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('favorites.index', $restaurant));
        $response->assertRedirect(route('login'));
    }

    // storeアクション（お気に入り追加機能）
    public function test_guest_cannot_favorite_store()
    {   
        $restaurant = Restaurant::factory()->create();
        $response = $this->post(route('favorites.store', $restaurant));
        $response->assertRedirect('login');
    }

    public function test_user_cannot_store_favorite()
    {   
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->post(route('favorites.store', $restaurant));
        $response->assertRedirect(route('subscription.create'));
    }

    public function test_subsc_subsc_user_can_access_favorite_index()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Qj9UuLrDOeQcDxNv4X1he93')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->post(route('favorites.store', $restaurant));
        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('restaurant_user', [
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
        ]);
    }

    public function test_admin_cannot_store_favorite()
    {   
        $admin = Admin::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->post(route('favorites.store', $restaurant));
        $response->assertRedirect(route('admin.home'));
    }

    // destroyアクション（お気に入り解除機能）
    public function test_guest_cannot_favorite_delete()
    {   
        $restaurant = Restaurant::factory()->create();
        $response = $this->delete(route('favorites.destroy', $restaurant));
        $response->assertRedirect('login');
    }

    public function test_user_cannot_favorite_destroy()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->delete(route('favorites.destroy', $restaurant));
        $response->assertRedirect(route('subscription.create'));
    }

    public function test_subsc_user_can_favorite_destroy()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Qj9UuLrDOeQcDxNv4X1he93')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();

        $user->favorite_restaurants()->attach($restaurant);
        $response = $this->actingAs($user)->delete(route('favorites.store', $restaurant));
        $response->assertStatus(302);

        $this->assertDatabaseMissing('restaurant_user', [
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
        ]);
    }

    public function test_admin_cannot_favorite_destroy()
    {   
        $admin = Admin::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->delete(route('favorites.destroy', $restaurant));
        $response->assertRedirect(route('admin.home'));
    }
}
