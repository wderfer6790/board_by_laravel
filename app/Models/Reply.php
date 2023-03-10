<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $table = '';

    protected $fillable = [];

    protected $dateFormat = 'Y-m-d H:i:s';

    public function article() {
        return $this->belongsTo('App\Models\Article', 'article_id', 'id');
    }

    public function file() {
        return $this->hasOne('App\Models\File', 'id', 'file_id');
    }

}
