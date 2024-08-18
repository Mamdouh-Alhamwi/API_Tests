<?php

namespace Database\Seeders;

use App\Enums\UserRole;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

       /* User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/

        //User::factory(10)->create();

        /*
        // admin
        $user = new User();
        $user->name = 'admin';
        $user->email = 'admin@example.com';
        $user->password = bcrypt('password123'); // set a password for the admin user
        $user->role = UserRole::ADMIN; // set the role to admin
        $user->save();
        */
    }
}
