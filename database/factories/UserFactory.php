<?php

namespace Database\Factories;

use App\Models\{User, File};
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

    /**
     * @return UserFactory
     */
    public function configure()
    {
        return $this->afterMaking(function (User $user) {
            // user id가 생성되지 않은 시점
        })->afterCreating(function (User $user) {
            if (File::count() > 0) {
                $fileId = File::take(1)->inRandomOrder()->value('id');
                if (!$fileId) {
                    throw new Exception\DatabaseFactoriesException('user profile image not found');
                }
                $user->file()->attach($fileId);
            }
        });
    }
}
