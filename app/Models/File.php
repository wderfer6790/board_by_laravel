<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'file';

    protected $fillable = [
        'files', 'create_at'
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    public function article() {
        return $this->hasOne('App\Models\Article', 'file_id', 'id');
    }

    public function reply() {
        return $this->hasOne('App\Models\Reply', 'file_id', 'id');
    }
}
