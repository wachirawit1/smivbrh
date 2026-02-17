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
        User::create([
            'prefix' => 'นาย',
            'fname' => 'System',
            'lname' => 'Admin',
            'username' => 'smivbrh',
            'area' => 'เมืองบุรีรัมย์',
            'amphoe' => 'เมืองบุรีรัมย์',
            'role' => 'admin',
            'is_approved' => true,
            'password' => Hash::make('smivbrh'),
        ]);

        // Sample User
        User::create([
            'prefix' => 'นางสาว',
            'fname' => 'Test',
            'lname' => 'Medical Staff',
            'username' => 'staff1',
            'area' => 'เมืองบุรีรัมย์',
            'amphoe' => 'เมืองบุรีรัมย์',
            'role' => 'user',
            'is_approved' => true,
            'password' => Hash::make('password'),
        ]);
    }
}
