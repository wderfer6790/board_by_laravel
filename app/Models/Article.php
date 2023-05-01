<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'article';

    protected $fillable = [
        'user_id', 'subject', 'content', 'views', 'likes', 'create_at', 'update_at'
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $casts = [
        'updated_at' => 'datetime:Y-m-d',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function replies()
    {
        return $this->hasMany('App\Models\Reply', 'article_id')
            ->withTrashed()
            ->whereNull('parent_id');
    }

    public function files()
    {
        return $this->morphToMany(File::class, 'fileable', 'fileable');
    }

    /**
     * 조회수, 좋아요수 업데이트
     *
     * @param $id
     * @param $type
     * @return void
     * @throws Exception
     * @return int
     */
    public static function increaseCount($id, $type) {
        if (!in_array($type, ['views', 'likes'])) {
            throw new \App\Models\Exception("increaseCount: type invalid");
        }

        if (!$article = Article::find($id)) {
            throw new \App\Models\Exception("increaseCount: article not found");
        }

        $article->timestamps = false;
        if (!$article->update([
            $type => $article->{$type} + 1
        ])) {
            throw new \App\Models\Exception("increaseCount: article {$type} update fail");
        }

        return $article->{$type};
    }

}
