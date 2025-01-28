<?php

namespace Tests\Feature\Admin;

use Database\Factories\UserFactory;
use Database\Factories\AdminFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_home() {

        $response = $this->get(route('admin.home'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_cannot_access_admin_home() {
        $user = UserFactory::new()->create();
        $response = $this->actingAs($user)->get(route('admin.home'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_access_admin_home() {
        $admin = AdminFactory::new()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.home'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.home');
    }
}
