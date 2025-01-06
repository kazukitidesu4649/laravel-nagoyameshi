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

    // ログイン済みの一般ユーザーは管理者側の会員一覧ページにアクセスできない
    public function test_user_cannot_access_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('users.index', '$user'));

        $response->assertStatus(403);
    }

    // 未ログインのユーザーは管理者側の会員詳細ページにアクセスできない
    public function test_guest_cannot_access_show()
    {
        $user = User::factory()->create();

        $response = $this->get(route('users.show', '$user'));

        $response->assertRedirect(route('login'));
    }
}
