<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin 1
        User::create([
            'name' => 'Admin 1',
            'username' => 'admin1',
            'email' => 'admin1@admin.com',
            'password' => bcrypt('admin1'),
            'role' => 'admin'
        ]);
        
        // Admin 2
        User::create([
            'name' => 'Admin 2',
            'username' => 'admin2',
            'email' => 'admin2@admin.com',
            'password' => bcrypt('admin2'),
            'role' => 'admin'
        ]);
        
        // Admin 3
        User::create([
            'name' => 'Admin 3',
            'username' => 'admin3',
            'email' => 'admin3@admin.com',
            'password' => bcrypt('admin3'),
            'role' => 'admin'
        ]);
    }
}