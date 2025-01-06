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
}
