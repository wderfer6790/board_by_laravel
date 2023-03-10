<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $email = $this->faker->unique()->safeEmail;
        return [
            'email' => $email,
            'password' => Hash::make(substr($email, 0, 8)),
            'name' => $this->faker->name,
            'email_verified_at' => $this->faker->date('Y-m-d H:i:s'),
        ];
    }
}
