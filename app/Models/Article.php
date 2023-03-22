<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'article';

    protected $fillable = [
        'user_id', 'subject', 'content', 'view', 'like', 'create_at', 'update_at'
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $casts = [
        'updated_at' => 'datetime:Y-m-d',
    ];

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function replies() {
        return $this->hasMany('App\Models\Reply', 'article_id', 'id');
    }

    public function files() {
        return $this->morphToMany(File::class, 'fileable', 'fileable');
    }
}
