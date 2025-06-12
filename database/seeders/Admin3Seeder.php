<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Admin3Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Tiga',
            'username' => 'admin3',
            'email' => 'admin3@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'sub_role' => 'admin3',
        ]);
    }
}
