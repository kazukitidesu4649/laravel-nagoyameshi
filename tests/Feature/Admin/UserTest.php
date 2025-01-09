<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Database\Factories\UserFactory;
use Database\Factories\AdminFactory;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // 未ログインユーザーが会員一覧ページにアクセスできないテスト
    public function test_guest_cannot_access_index()
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会員一覧ページにアクセスできない
    public function test_user_cannot_access_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.users.index', '$user'));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の会員一覧ページにアクセスできる
    public function test_admin_cannot_access_index()
    {
        $admin = AdminFactory::new()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.users.index', '$admin'));

        $response->assertStatus(200);
    }

    // 未ログインのユーザーは管理者側の会員詳細ページにアクセスできない
    public function test_guest_cannot_access_show()
    {
        $response = $this->get(route('admin.users.show', '$user'));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会員詳細ページにアクセスできない
    public function test_user_cannot_access_show()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.users.show', '$user'));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の会員詳細ページにアクセスできる
    public function test_admin_cannot_access_show()
    {
        $admin = AdminFactory::new()->create();

        $user = UserFactory::new()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.users.show', ['user' => $user->id]));

        $response->assertStatus(200);
    }
}
