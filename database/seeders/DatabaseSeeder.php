<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Membuat user biasa
        User::create([
            'name' => 'User Biasa',
            'username' => 'user',
            'email' => 'user@example.com',
            'password' => bcrypt('user'),
            'role' => 'user'
        ]);

        // Menjalankan seeder lainnya
        $this->call([
            Admin1Seeder::class,
            Admin2Seeder::class,
            Admin3Seeder::class,
            ServiceScheduleSeeder::class,
            DataItemSeeder::class
        ]);
    }
}
