<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Article, User, File};

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first();
        if(!$user) {
            throw new Exception\DatabaseFactoriesException('user not found');
        }

        $updated_at = $this->faker->date("Y-m-d H:i:s");
        $views = $this->faker->numberBetween(0, 9999);
        return [
            'user_id' => $user->id,
            'subject' => $this->faker->text(64),
            'content' => $this->faker->text(1024),
            'like' => $this->faker->numberBetween(0, $views),
            'view' => $views,
            'created_at' => $this->faker->date("Y-m-d H:i:s", $updated_at),
            'updated_at' => $updated_at,
        ];
    }
}
