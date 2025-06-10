<?php

namespace App\Broadcasting;


use App\Models\Chats_has_user;
use App\Models\User;

class ChatChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, $id): array|bool
    {
        if (Chats_has_user::query()->where('users_idUser', $user->id)->where('chats_idChat', $id)) {
            return true;
        }
        return false;
    }
}
