<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->randomNumber(5),
            'image' => $this->faker->imageUrl(),

            'name' => $this->faker->name,
            'company' => $this->faker->company,
            
            'address' => $this->faker->address,
            'department' => $this->faker->secondaryAddress,
            'district' => $this->faker->citySuffix,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'email' => $this->faker->unique()->email,
            'phone' => $this->faker->phoneNumber,
            
            'cuit' => $this->faker->unique()->randomNumber(8),
        ];
    }
}
