<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chats_has_user extends Model
{
    protected $fillable = ['chats_idChat', 'users_idUser'];
    public function users()
    {
        $this->hasMany(User::class, 'users_idUser');
    }
}
