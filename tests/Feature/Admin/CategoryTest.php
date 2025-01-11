<?php

namespace Tests\Feature\Admin;

use Database\Factories\UserFactory;
use Database\Factories\AdminFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（カテゴリ一覧ページ）
    public function test_guest_cannot_admin_categories_index()
    {
        $response = $this->get('/admin/categories');
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_admin_categories_index()
    {
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user)->get('/admin/categories');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_cannot_admin_categories_index()
    {
        $admin = AdminFactory::new()->create();

        $response = $this->actingAs($admin, 'admin')->get('/admin/categories');
        $response->assertStatus(200);
    }


}
