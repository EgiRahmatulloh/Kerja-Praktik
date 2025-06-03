<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Admin2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'admin2',
            'username' => 'admin2',
            'email' => 'admin2@admin.com',
            'password' => bcrypt('admin2'),
            'role' => 'admin'
        ]);
    }
}