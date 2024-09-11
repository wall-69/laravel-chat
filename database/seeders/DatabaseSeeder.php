<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'nickname' => 'frajer',
            'email' => 'a@a.a',
            "password" => '$2y$12$cVFF9K.gLEZv.KqpeV0V5OUyaauXPAr41xeZIbb6qv47FRJBfjw3q',
            "profile_picture" => "storage/img/pfp/Kkc4sWiAT8zBkLCbgAF7SwkqBRLkONvyqF9G4WgU.png",
        ]);
        User::factory()->create([
            'nickname' => 'netusim',
            'email' => 'b@b.b',
            "password" => '$2y$12$cVFF9K.gLEZv.KqpeV0V5OUyaauXPAr41xeZIbb6qv47FRJBfjw3q',
            "profile_picture" => "storage/img/pfp/FghEZYNknYU3iVY4kN7Oq7uSqtMl33GGICwm0AF5.jpg",
        ]);
        User::factory()->create([
            'nickname' => 'podnikatel',
            'email' => 'c@c.c',
            "password" => '$2y$12$cVFF9K.gLEZv.KqpeV0V5OUyaauXPAr41xeZIbb6qv47FRJBfjw3q',
            "profile_picture" => "storage/img/pfp/7vTFe9ThR3lYrarHn2J63aLw4BIco466XtoEO0yd.jpg",
        ]);
    }
}
