<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\UserSeeder;
use App\Models\User;
use Database\Factories\UserFactory;
use Database\Factories\AdminFactory;

class HomeTest extends TestCase
{
    use RefreshDatabase;
    
    // homeにアクセスできる
    public function tests_guest_can_access_home()
    {
        $response = $this->get('/');
        $response->assertViewIs('home');
    }

    public function tests_user_can_access_home()
    {
        $user = UserFactory::new()->create();
        $response = $this->actingAs($user)->get('/');

        $response->assertViewIs('home');
    }

    public function tests_admin_can_access_home()
    {
        $admin = AdminFactory::new()->create();
        $response = $this->actingAs($admin, 'admin')->get('/');

        $response->assertRedirect('/admin/home');
    }
}
