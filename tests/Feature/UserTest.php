<?php

namespace Tests\Feature;


use App\Models\User;
use App\Models\Admin;
use Database\Factories\AdminFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Psy\Readline\Hoa\EventListens;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（会員情報ページ）
    public function test_guest_cannot_access_user_index()
    {
        $response = $this->get(route('user.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_acccess_user_index()
    {
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user)->get(route('user.index'));
        $response->assertStatus(200);
        $response->assertViewIs('user.index');
    }

    public function test_admin_cannot_access_user_index()
    {
        $admin = AdminFactory::new()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('user.index'));
        $response->assertRedirect(route('admin.home'));
    }

    // editアクション （会員情報編集ページ）
    public function test_guest_cannot_edit_user_edit()
    {   
        $user = UserFactory::new()->create();
        $response = $this->get(route('user.edit', ['user' => $user->id]));
        $response->assertRedirect(route('login'));
    }

    public function test_user_cannot_edit_otheruser_edit()
    {   
        $user = UserFactory::new()->create();
        $otherUser = UserFactory::new()->create();

        $response = $this->actingAs($user)->get(route('user.edit', ['user' => $otherUser->id]));
        $response->assertRedirect(route('user.index'));
        // $response->assertSessionHas('error_messsage', '不正なアクセスです。');
    }

    public function test_user_can_access_own_user_edit()
    {
        $user = Userfactory::new()->create();

        $response = $this->actingAs($user)->get(route('user.edit', ['user' => $user->id]));
        $response->assertStatus(200);
        $response->assertViewIs('user.edit'); 
    }

    public function test_admin_cannot_access_user_edit()
    {
        $admin = AdminFactory::new()->create();
        $user = UserFactory::new()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('user.edit', ['user' => $user->id]));  // ユーザーIDを渡す
        $response->assertRedirect(route('admin.home')); 
    }

    // updateアクション（会員情報更新機能）
    public function test_guest_cannot_update_user_information()
    {
        $user = User::factory()->create();

        $response = $this->patch(route('user.update', ['user' => $user->id]), [
            'name' => 'New Name',
            'kana' => 'カナカナ',      
            'email' => 'newemail@example.com', 
            'postal_code' => '1234567', 
            'address' => '新しい住所',    
            'phone_number' => '08012345678', 
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_user_cannot_update_other_user_information()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('user.update', ['user' => $otherUser->id]), [
            'name' => 'New Name',
            'kana' => 'カナカナ',      
            'email' => 'newemail@example.com', 
            'postal_code' => '1234567', 
            'address' => '新しい住所',    
            'phone_number' => '08012345678', 
        ]);

        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas('error_message', '不正なアクセスです。');
    }

    public function test_authenticated_user_can_update_own_user_edit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('user.update', ['user' => $user->id]), [
            'name' => 'New Name',
            'kana' => 'カナカナ',      
            'email' => 'newemail@example.com', 
            'postal_code' => '1234567', 
            'address' => '新しい住所',    
            'phone_number' => '08012345678', 
        ]);
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas('flash_message', '会員情報を編集しました。');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
    }

    public function test_admin_cannot_update_user_information()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($admin, 'admin')->patch(route('user.update', ['user' => $user->id]), [
            'name' => 'New Name',
            'kana' => 'カナカナ',      
            'email' => 'newemail@example.com', 
            'postal_code' => '1234567', 
            'address' => '新しい住所',    
            'phone_number' => '08012345678', 
        ]);

        $response->assertRedirect(route('admin.home')); 
    }
}