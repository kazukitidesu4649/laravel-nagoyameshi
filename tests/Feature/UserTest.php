<?php

namespace Tests\Feature;


use App\Models\User;
use App\Models\Admin;
use Database\Factories\AdminFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Psy\Readline\Hoa\EventListens;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（会員情報ページ）
    public function test_guest_cannot_access_user_index()
    {
        $response = $this->get(route('user.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_acccess_user_index()
    {
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user)->get(route('user.index'));
        $response->assertStatus(200);
        $response->assertViewIs('user.index');
    }

    public function test_admin_cannot_access_user_index()
    {
        $admin = AdminFactory::new()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('user.index'));
        $response->assertRedirect(route('admin.home'));
    }
}
