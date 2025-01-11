<?php

namespace Tests\Feature\Admin;

use Database\Factories\UserFactory;
use Database\Factories\AdminFactory;
use Database\Factories\CategoryFactory;
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

    // storeアクション（カテゴリ登録機能）
    public function test_guest_cannot_store_categories()
    {
        $category = CategoryFactory::new()->create();

        $response = $this->post('/admin/categories',[$category]);
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_store_categories()
    {   
        $user = UserFactory::new()->create();
        $category = CategoryFactory::new()->create();

        $response = $this->actingAs($user)->post('/admin/categories',[$category]);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_store_categories()
    {   
        $admin = AdminFactory::new()->create();
        $category = CategoryFactory::new()->create();

        $response = $this->actingAs($admin, 'admin')->post('/admin/categories',[$category]);
        $response->assertStatus(302);
    }
}
