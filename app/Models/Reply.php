<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $table = 'reply';

    protected $fillable = [
        'article_id', 'parent_id', 'user_id', 'content'
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    public function article() {
        return $this->belongsTo('App\Models\Article', 'article_id', 'id');
    }
}
