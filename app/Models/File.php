<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    const CREATED_AT = 'uploaded_at';

    const UPDATED_AT = null;

    protected $table = 'file';

    protected $fillable = [
        'name', 'path', 'mime'
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    public function users() {
        return $this->morphedByMany(User::class, 'fileable', 'fileable');
    }

    public function articles() {
        return $this->morphedByMany(Article::class, 'fileable', 'fileable');
    }

    public function replies() {
        return $this->morphedByMany(Reply::class, 'fileable', 'fileable');
    }
}
