<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'Tears on January 5th',
            'last_name' => '.',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'avatar' => 'images/profile/me.png',
            'cover_image' => 'images/profile/nbn.png'
        ]);

        // Создаем обычного пользователя
        User::create([
            'first_name' => 'Матвей',
            'last_name' => 'Иванов',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'avatar' => 'images/profile/stas.png',
            'cover_image' => 'images/profile/KANEKI KEN SAD.png'
        ]);
    }
} 
