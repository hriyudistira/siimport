<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@incoe.co.id'],
            [
                'name' => 'Administrator',
                'email' => 'admin@incoe.co.id',
                'password' => Hash::make('admin123'),
            ]
        );
    }
}
