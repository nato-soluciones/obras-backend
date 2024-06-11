<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                "firstname" => "Tomas",
                "lastname" => "Gimenez",
                "email" => "tomasgimenez11@gmail.com",
                "password" => bcrypt("CCian2023"),
                'email_verified_at' => now(),
            ],
            [
                "firstname" => "Super",
                "lastname" => "Admin",
                "email" => "superadmin@gmail.com",
                "password" => bcrypt("Nato2024"),
                'email_verified_at' => now(),
            ],
            [
                "firstname" => "Arqui",
                "lastname" => "tecto",
                "email" => "Arquitecto@gmail.com",
                "password" => bcrypt("Nato2024"),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            $existingUser = User::where('email', $user['email'])->first();
            if (!$existingUser) {
                $user = User::create($user);
            }
        }
    }
}
