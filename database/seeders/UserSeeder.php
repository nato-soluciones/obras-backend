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
                "firstname" => "Paco Nicolas",
                "lastname" => "Miranda",
                "role" => "OWNER",
                "email" => "paconicolasmiranda@gmail.com",
                "password" => bcrypt("CCian2023"),
                'email_verified_at' => now(),
            ],
            [
                "firstname" => "Tomas",
                "lastname" => "Gimenez",
                "role" => "OWNER",
                "email" => "tomasgimenez11@gmail.com",
                "password" => bcrypt("CCian2023"),
                'email_verified_at' => now(),
            ],
            [
              "firstname" => "Pablo",
              "lastname" => "Carnevale",
              "role" => "OWNER",
              "email" => "pablo.carnevale7@gmail.com",
              "password" => bcrypt("CCian2023"),
              'email_verified_at' => now(),
          ],
        ];

        foreach ($users as $user) {
            $existingUser = User::where('email', $user['email'])->first();

            if (!$existingUser) {
                User::create($user);
            }
        }
    }
}
