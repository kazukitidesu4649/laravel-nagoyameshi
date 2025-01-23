<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\Review;
use Database\Factories\AdminFactory;
use Database\Factories\UserFactory;
use Database\Factories\ReviewFactory;
use Database\Factories\RestaurantFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（レビュー一覧ページ）
    public function test_guest_cannot_access_review_index_page()
    {
        $restaurant = RestaurantFactory::new()->create();

        $response = $this->get(route('restaurants.reviews.index', $restaurant));
        $response->assertRedirect('/login');
    }

    public function test_user_can_access_review_index_page()
    {
        $user = UserFactory::new()->create();
        $restaurant = RestaurantFactory::new()->create();

        $response = $this->actingAs($user)->get(route('restaurants.reviews.index', $restaurant));
        $response->assertStatus(200);
        $response->assertViewIs('reviews.index');
    }

    public function test_authenticated_paid_user_can_access_user_review_index_page()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1Qj9UuLrDOeQcDxNv4X1he93')->create('pm_card_visa');

        $restaurant = Restaurant::factory()->create();
      
        $response = $this->actingAs($user)
            ->get(route('restaurants.reviews.index', $restaurant));

        $response->assertStatus(200);
        $response->assertViewIs('reviews.index');
    }

    public function test_authenticated_admin_cannot_access_user_review_index_page()
    {
        $admin = AdminFactory::new()->create();
        $restaurant = Restaurant::factory()->create();

        $response=$this->actingAs($admin, 'admin')->get(route('restaurants.reviews.index', $restaurant));
        $response->assertRedirect(route('admin.home'));
    }
}
