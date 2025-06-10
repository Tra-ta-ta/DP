<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
   protected $fillable = ['users_idUser', 'messageText'];
   public function user()
   {
      return $this->belongsTo(User::class, 'users_idUser');
   }
   public function chats()
   {
      return $this->belongsToMany(
         Chat::class,
         'chats_has_message',
         'messages_idMessage',
         'chats_idChat'
      );
   }
}
