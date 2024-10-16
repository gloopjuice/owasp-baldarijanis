<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'girts',
            'name' => 'girts',
            'email' => 'girts@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('girts'),
            'role_id' => 1,
        ]);
    }
}
