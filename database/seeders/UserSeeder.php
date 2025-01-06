<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('users')->insert([
        //     'name' => '侍',
        //     'kana' => 'さむらい',
        //     'email' => 'samurai@example,com',
        //     'email_verified_at' => '2025-01-01 00:00:00',
        //     'password' => Hash::make('password'),
        //     'postal_code' => '6500012',
        //     'address' => '兵庫県神戸市中央区相生町',
        //     'phone_number' => '09022223333',
        //     'birthday' => '',
        //     'occupation' => '',
        //     'remember_token' => '',
        //     'created_at' => '2024-12-12 11:11:11',
        //     'updated_at' => '2024-12-22 11:11:11'
        // ]);

        User::factory()->count(100)->create();

    }
}
