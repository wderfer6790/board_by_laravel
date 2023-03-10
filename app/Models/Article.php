<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'article';

    protected $fillable = [
        'user_id', 'subject', 'content', 'file', 'views', 'create_at', 'update_at'
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function reply() {
        return $this->hasMany('App\Models\Reply', 'article_id', 'id');
    }

    public function file() {
        return $this->hasOne('App\Models\File', 'id', 'file_id');
    }
}
