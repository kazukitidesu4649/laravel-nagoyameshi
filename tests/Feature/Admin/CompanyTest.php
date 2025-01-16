<?php

namespace Tests\Feature\Admin;

use Database\Factories\UserFactory;
use Database\Factories\AdminFactory;
use Database\Factories\CompanyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\Admin\CompanyController;
use Illuminate\Foundation\Auth\User;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（会社概要ページ）
    public function tests_guest_cannot_admin_company_index()
    {
        $response = $this->get('/admin/company');
        $response->assertRedirect('/admin/login');
    }

    public function tests_user_cannot_admin_company_index()
    {
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user)->get('/admin/company');
        $response->assertRedirect('/admin/login');
    }

    public function tests_admin_can_admin_company_index()
    {
        $admin = AdminFactory::new()->create();
        $company = CompanyFactory::new()->create();

        $response = $this->actingAs($admin, 'admin')->get('/admin/company');
        $response->assertStatus(200);
    }

    // editアクション（会社概要編集ページ）
    public function tests_guest_cannot_admin_company_edit()
    {
        $company = CompanyFactory::new()->create();

        $response = $this->get("/admin/company/{$company->id}/edit");
        $response->assertRedirect('/admin/login');
    }

    public function tests_user_cannot_admin_company_edit()
    {
        $user = UserFactory::new()->create();
        $company = CompanyFactory::new()->create();

        $response = $this->actingAs($user)->get("/admin/company/{$company->id}/edit");
        $response->assertRedirect('/admin/login');
    }

    public function tests_admin_can_admin_company_edit()
    {
        $admin = AdminFactory::new()->create();
        $company = CompanyFactory::new()->create();

        $response = $this->actingAs($admin, 'admin')->get("/admin/company/{$company->id}/edit");
        $response->assertStatus(200);
    }

    // updateアクション（会社概要更新機能）
    public function tests_guest_cannot_admin_company_update()
    {
        $company = CompanyFactory::new()->create();
        $response = $this->patch(route('admin.company.update', $company), [
            'name' => '新しい会社名',
            'postal_code' => '1111111',
            'address' => '新しい住所',
            'representative' => '新しい代表者',
            'establishment_date' => '2020-01-01',
            'capital' => '1000000',
            'business' => '新しい事業内容',
            'number_of_employees' => 100,
        ]);

        $response->assertRedirect('/admin/login');
    }

    public function tests_user_cannot_update_company()
    {
        $user = UserFactory::new()->create();
        $company = CompanyFactory::new()->create();
        $response = $this->actingAs($user)->patch(route('admin.company.update', $company), [
            'name' => '新しい会社名',
            'postal_code' => '1111111',
            'address' => '新しい住所',
            'representative' => '新しい代表者',
            'establishment_date' => '2020-01-01',
            'capital' => '1000000',
            'business' => '新しい事業内容',
            'number_of_employees' => 100,
        ]);
        $response->assertRedirect('/admin/login'); 
    }

    public function tests_admin_can_admin_update_company()
    {
        $admin = AdminFactory::new()->create();
        $company = CompanyFactory::new()->create();

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.company.update', $company), [
            'name' => '新しい会社名',
            'postal_code' => '1111111',
            'address' => '新しい住所',
            'representative' => '新しい代表者',
            'establishment_date' => '2020-01-01',
            'capital' => '1000000',
            'business' => '新しい事業内容',
            'number_of_employees' => 100,
        ]);
        $response->assertRedirect(route('admin.company.index')); 
        $this->assertDatabaseHas('companies', ['name' => '新しい会社名']);
    }
}
