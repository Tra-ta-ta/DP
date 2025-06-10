<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use SoftDeletes;
    protected $fillable = ['status'];

    public function messages()
    {
        return $this->belongsToMany(
            Message::class,
            'chats_has_messages',
            'chats_idChat',
            'messages_idMessage'
        );
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'chats_has_users',
            'chats_idChat',
            'users_idUser'
        );
    }
}
