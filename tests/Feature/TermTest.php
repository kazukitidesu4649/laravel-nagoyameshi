<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use App\Models\Term;
use Database\Factories\TermFactory;
use Database\Factories\UserFactory;
use Database\Factories\AdminFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TermTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_term() {
        $term = TermFactory::new()->create();

        $response = $this->get(route('terms.index'));
        $response->assertStatus(200);
        $response->assertViewIs('terms.index');
    }

    public function test_user_can_access_term() {
        $term = TermFactory::new()->create();
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user)->get(route('terms.index'));
        $response->assertStatus(200);
        $response->assertViewIs('terms.index');
    }

    public function test_admin_cannot_access_term() {
        $term = TermFactory::new()->create();
        $admin = AdminFactory::new()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('terms.index'));
        $response->assertRedirect(route('admin.home'));
    }
}
