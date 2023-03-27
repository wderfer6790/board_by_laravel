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
        $userId = User::take(1)->inRandomOrder()->value('id');
        if(!$userId) {
            throw new Exception\DatabaseFactoriesException('user not found');
        }

        $updated_at = $this->faker->date("Y-m-d H:i:s");
        $views = $this->faker->numberBetween(0, 9999);
        return [
            'user_id' => $userId,
            'subject' => $this->faker->text(64),
            'content' => $this->faker->text(1024),
            'views' => $views,
            'likes' => $this->faker->numberBetween(0, $views),
            'created_at' => $this->faker->date("Y-m-d H:i:s", $updated_at),
            'updated_at' => $updated_at,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Article $article) {
            if (File::count() > 0 && random_int(0, 100) < 90) {
                $fileId = File::take(1)->inRandomOrder()->value('id');
                if (!$fileId) {
                    throw new Exception\DatabaseFactoriesException('user profile image not found');
                }
                $article->files()->attach($fileId);
            }
        });
    }


}
