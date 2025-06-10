<?php

namespace App\Http\Controllers\Auth;

use App\Events\ChatChange;
use App\Events\MessageSend;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Chats_has_message;
use App\Models\Chats_has_user;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatConrtoller extends Controller
{
    public function join()
    {
        $chatsHasUser = Chats_has_user::where('users_idUser', '=', Auth::user()->id)->first();

        if (!$chatsHasUser) {
            $chat = Chat::create();
            Chats_has_user::create([
                'chats_idChat' => $chat->id,
                'users_idUser' => Auth::user()->id
            ]);

            return response()->json($chat->id, 200);
        }

        $chat = Chat::where('id', '=', $chatsHasUser->chats_idChat)->first();

        if (!$chat) {
            $chat = Chat::create();
            Chats_has_user::create([
                'chats_idChat' => $chat->id,
                'users_idUser' => Auth::user()->id
            ]);

            return response()->json($chat->id, 200);
        }
        $chat->update([
            'status' => 'Новый'
        ]);
        return response()->json($chat->id, 200);
    }

    public function sendMessage(Request $request)
    {
        $message = new Message();
        $message->messageText = $request->message;
        $message->users_idUser = $request->user()->id;
        $message->save();

        broadcast(new MessageSend(Message::with('user')->where('id', $message->id)->first(), $request->idChat));

        $messageChat = new Chats_has_message();
        $messageChat->chats_idChat = $request->idChat;
        $messageChat->messages_idMessage = $message->id;
        $messageChat->save();

        $chat = Chat::find($request->idChat);
        $chat->update([
            'status' => 'Новый'
        ]);

        broadcast(new ChatChange($chat));

        return response()->json($message, 200);
    }

    public function getNewChats(Request $request)
    {
        $id = $request->query('id', null);

        $chats = Chat::with(['users', 'messages'])->where('status', 'Новый');

        if (!is_null($id)) {
            $chats->where('id', $id);
        }

        return response()->json($chats->get(), 200);
    }
    public function sendMessageFromMenager(Request $request)
    {
        $message = new Message();
        $message->messageText = $request->message;
        $message->users_idUser = $request->user()->id;
        $message->save();

        broadcast(new MessageSend(Message::with('user')->where('id', $message->id)->first(), $request->idChat));

        $messageChat = new Chats_has_message();
        $messageChat->chats_idChat = $request->idChat;
        $messageChat->messages_idMessage = $message->id;
        $messageChat->save();

        $chat = Chat::query()->where('id', $request->idChat)->first();
        $chat->update([
            'status' => 'В обработке'
        ]);

        broadcast(new ChatChange($chat));

        return response()->json($message, 200);
    }
    public function getMessages(Request $request)
    {
        $chat = Chat::with('messages.user')->where('id', $request->query('chatId'))->first();

        return response()->json($chat->messages, 200);
    }
}
