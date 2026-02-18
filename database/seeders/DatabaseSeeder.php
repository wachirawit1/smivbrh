<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user according to requirement
        User::updateOrCreate(
            ['username' => 'smivbrh'], // 🔍 เงื่อนไขค้นหา
            [
                'prefix' => 'นาย',
                'fname' => 'System',
                'lname' => 'Admin',
                'area' => 'เมืองบุรีรัมย์',
                'amphoe' => 'เมืองบุรีรัมย์',
                'role' => 'admin',
                'is_approved' => true,
                'password' => Hash::make('smivbrh'),
            ]
        );


        // Sample User
        User::updateOrCreate(
            ['username' => 'staff1'],
            [
                'prefix' => 'นางสาว',
                'fname' => 'Test',
                'lname' => 'Medical Staff',
                'area' => 'เมืองบุรีรัมย์',
                'amphoe' => 'เมืองบุรีรัมย์',
                'role' => 'user',
                'is_approved' => true,
                'password' => Hash::make('password'),
            ]
        );
    }
}
