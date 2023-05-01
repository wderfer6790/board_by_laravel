<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reply';

    protected $fillable = [
        'article_id', 'parent_id', 'user_id', 'content'
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    public function article() {
        return $this->belongsTo('App\Models\Article', 'article_id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function file() {
        return $this->morphToMany(File::class, 'fileable', 'fileable');
    }

    public function parent() {
        return $this->belongsTo(Reply::class, 'parent_id', 'id')
            ->whereNull('parent_id');
    }

    public function child() {
        return $this->hasMany(Reply::class, 'parent_id', 'id')
            ->withTrashed();
    }
}
