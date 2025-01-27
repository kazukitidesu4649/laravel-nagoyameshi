<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use App\Models\Company;
use Database\Factories\CompanyFactory;
use Database\Factories\UserFactory;
use Database\Factories\AdminFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_company() {
        $company = CompanyFactory::new()->create();

        $response = $this->get(route('company.index'));
        $response->assertStatus(200);
        $response->assertViewIs('company.index');
    }

    public function test_user_can_access_company() {
        $company = CompanyFactory::new()->create();
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user)->get(route('company.index'));
        $response->assertStatus(200);
        $response->assertViewIs('company.index');
    }

    public function test_admin_cannot_access_company() {
        $company = CompanyFactory::new()->create();
        $admin = AdminFactory::new()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('company.index'));
        $response->assertRedirect(route('admin.home'));
    }
}
