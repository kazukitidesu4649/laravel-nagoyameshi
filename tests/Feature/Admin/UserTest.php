<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // 未ログインユーザーが会員一覧ページにアクセスできないテスト
    public function test_guest_cannot_access_index()
    {
        $response = $this->get(route('users.index'));

        $response->assertRedirect(route('login'));
    }

    // 一般ユーザーが会員一覧ページにアクセスできないことをテスト
    public function test_non_admin_user_cannot_access_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('users.index'));

        $response->assertStatus(403);
    }

    // 管理者ユーザーが会員一覧ページにアクセスできることをテスト
    public function test_admin_can_access_index()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('users.index');
    }

    // 未ログインユーザーが会員詳細ページにアクセスできないことをテスト
    public function test_guest_cannot_access_show()
    {
        $user = User::factory()->create();

        $response = $this->get(route('users.show', $user));

        $response->assertRedirect(route('login'));
    }

    // 一般ユーザーが会員詳細ページにアクセスできないことをテスト
    public function test_non_admin_user_cannot_access_show()
    {
        $user = User::factory()->create();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($user)->get(route('users.show', $targetUser));

        $response->assertStatus(403);
    }

    // 管理者ユーザーが会員詳細ページにアクセスできることをテスト
    public function test_admin_can_access_show()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $targetUser = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('users.show', $targetUser));

        $response->assertStatus(200);
        $response->assertViewIs('Users.show');
        $response->assertViewHas('user', $targetUser);
    }
}
