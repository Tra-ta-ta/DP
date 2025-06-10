<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chats_has_message extends Model
{
    protected $fillable = ['messages_idMessage', 'chats_idChat'];
    public function messages()
    {
        return $this->hasMany(Message::class, 'messages_idMessage');
    }
}
