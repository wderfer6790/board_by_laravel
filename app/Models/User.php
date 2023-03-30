<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory, Notifiable, MustVerifyEmail;

    protected $table = 'user';

    protected $fillable = [
        'email', 'name', 'passwrod', 'email_verified_at', 'remember_token', 'create_at', 'update_at'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $dateFormat = 'Y-m-d H:i:s';

    public function articles() {
        return $this->hasMany('App\Models\Article', 'user_id');
    }

    public function replies() {
        return $this->hasMany('App\Models\Reply', 'user_id');
    }

    public function file() {
        return $this->morphToMany(File::class, 'fileable', 'fileable');
    }

}
