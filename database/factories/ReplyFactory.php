<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Reply, User, Article, File};

class ReplyFactory extends Factory
{
    protected $model = Reply::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $article_id = Article::take(1)->inRandomOrder()->value('id');
        if (!$article_id) {
            throw new Exception\DatabaseFactoriesException('article empty');
        }

        $user_id = User::take(1)->inRandomOrder()->value('id');
        if (!$user_id) {
            throw new Exception\DatabaseFactoriesException('user not found');
        }

        return [
            'article_id' => $article_id,
            'user_id' => $user_id,
            'content' => $this->faker->text(128)
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Reply $reply) {
            if (File::count() > 0 && random_int(0, 99) > 79) {
                $fileId = File::take(1)->inRandomOrder()->value('id');
                if (!$fileId) {
                    throw new Exception\DatabaseFactoriesException('file not found');
                }
                $reply->file()->attach($fileId);
            }

            if (random_int(0, 99) > 59) {
                $userId = User::take(1)->inRandomOrder()->value('id');
                if (!$userId) {
                    throw new Exception\DatabaseFactoriesException('user not found');
                }

                $child = new Reply([
                    'article_id' => $reply->article_id,
                    'parent_id' => $reply->id,
                    'user_id' => $userId,
                    'content' => $this->faker->text(128),
                ]);
                $child->save();
                $child->refresh();
                if (random_int(0, 99 > 79)) {
                    $fileId = File::take(1)->inRandomOrder()->value('id');
                    if (!$fileId) {
                        throw new Exception\DatabaseFactoriesException('file not found');
                    }
                    $reply->file()->attach($fileId);
                }
            }
        });
    }
}
