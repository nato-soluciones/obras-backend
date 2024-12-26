<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Store;
use App\Models\UserStore;
use Illuminate\Database\Seeder;

class UserStoreSeeder extends Seeder
{
    public function run()
    {
        // Obtenemos algunos usuarios y tiendas existentes
        $users = User::all();
        $stores = Store::all();

        // Asignamos algunas tiendas a usuarios de manera aleatoria
        foreach ($users as $user) {
            // Asignamos entre 1 y 3 tiendas aleatorias a cada usuario
            $randomStores = $stores->random(rand(1, 3));
            
            foreach ($randomStores as $store) {
                UserStore::create([
                    'user_id' => $user->id,
                    'store_id' => $store->id
                ]);
            }
        }
    }
} 