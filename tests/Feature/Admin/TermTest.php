<?php

namespace Tests\Feature\Admin;

use Database\Factories\UserFactory;
use Database\Factories\AdminFactory;
use Database\Factories\TermFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class TermTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション
    public function tests_guest_cannot_acceess_term_index()
    {
        $responce = $this->get('/admin/terms');
        $responce->assertRedirect('/admin/login');
    }

    public function tests_user_cannot_acceess_term_index()
    {   
        $user = UserFactory::new()->create();
        $responce = $this->actingAs($user)->get('/admin/terms');
        $responce->assertRedirect('/admin/login');
    }

    public function tests_admin_can_acceess_term_index()
    {   
        $admin = AdminFactory::new()->create();
        $term = TermFactory::new()->create();
        $responce = $this->actingAs($admin, 'admin')->get('/admin/terms');
        $responce->assertStatus(200);
    }

    // editアクション
    public function tests_guest_cannot_acceess_term_edit()
    {
        $term = TermFactory::new()->create();
        $responce = $this->get("/admin/terms/{$term->id}/edit");
        $responce->assertRedirect('/admin/login');
    }

    public function tests_user_cannot_acceess_term_edit()
    {   
        $user = UserFactory::new()->create();
        $term = TermFactory::new()->create();
        $responce = $this->actingAs($user)->get("/admin/terms/{$term->id}/edit");
        $responce->assertRedirect('/admin/login');
    }

    public function tests_admin_can_acceess_term_edit()
    {   
        $admin = AdminFactory::new()->create();
        $term = TermFactory::new()->create();
        $responce = $this->actingAs($admin, 'admin')->get("/admin/terms/{$term->id}/edit");
        $responce->assertStatus(200);
    }

    // updateアクション
    public function tests_guest_cannot_acceess_term_update()
    {
        $term = TermFactory::new()->create();
        $responce = $this->patch("/admin/terms/{$term->id}", [
            'content' => '新しい概要'
        ]);
        $responce->assertRedirect('/admin/login');
    }

    public function tests_user_cannot_acceess_term_update()
    {
        $term = TermFactory::new()->create();
        $user = UserFactory::new()->create();
        $responce = $this->actingAs($user)->patch("/admin/terms/{$term->id}", [
            'content' => '新しい概要'
        ]);
        $responce->assertRedirect('/admin/login');
    }

    public function tests_admin_can_acceess_term_update()
    {
        $term = TermFactory::new()->create();
        $admin = AdminFactory::new()->create();
        $responce = $this->actingAs($admin, 'admin')->patch("/admin/terms/{$term->id}", [
            'content' => '新しい概要'
        ]);
        $responce->assertRedirect('/admin/terms');
        $this->assertDatabaseHas('terms', ['content' => '新しい概要']);
    }
}
