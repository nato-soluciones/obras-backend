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
                "firstname" => "Dueño",
                "lastname" => "CCian",
                "role" => "OWNER",
                "email" => "owner@ccian.com.ar",
                "password" => bcrypt("CCian2023"),
                'email_verified_at' => now(),
            ],
            [
                "firstname" => "Architect",
                "lastname" => "CCian",
                "role" => "ARCHITECT",
                "email" => "architect@ccian.com.ar",
                "password" => bcrypt("CCian2023"),
                'email_verified_at' => now(),
            ],
            [
                "firstname" => "Jefe de Obra",
                "lastname" => "CCian",
                "role" => "CONSTRUCTION_MANAGER",
                "email" => "jefedeobra@ccian.com.ar",
                "password" => bcrypt("CCian2023"),
                'email_verified_at' => now(),
            ],
            [
                "firstname" => "Administrativo",
                "lastname" => "CCian",
                "role" => "ADMINISTRATIVE",
                "email" => "administrativo@ccian.com.ar",
                "password" => bcrypt("CCian2023"),
                'email_verified_at' => now(),
            ],
            [
                "firstname" => "Cliente",
                "lastname" => "CCian",
                "role" => "CLIENT",
                "email" => "client@ccian.com.ar",
                "password" => bcrypt("CCian2023"),
                'email_verified_at' => now(),
            ],
            [
                "firstname" => "Contratista",
                "lastname" => "CCian",
                "role" => "CONTRACTOR",
                "email" => "contractor@ccian.com.ar",
                "password" => bcrypt("CCian2023"),
                'email_verified_at' => now(),
            ],
            [
                "firstname" => "Tomas",
                "lastname" => "Gimenez",
                "role" => "OWNER",
                "email" => "tomasgimenez11@gmail.com ",
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
