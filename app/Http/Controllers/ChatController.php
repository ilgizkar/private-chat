<?php

namespace App\Http\Controllers;

use App\Events\MsgReadEvent;
use App\Events\PrivateChatEvent;
use App\Http\Resources\ChatResource;
use App\Http\Resources\UserResource;
use App\Models\Session;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class ChatController extends Controller
{
    public function send(Session $session, Request $request)
    {
        $message = $session->messages()->create(['content' => $request->contents]);

        $chat = $message->createForSend($session->id);

        $message->createForReceive($session->id, $request->to_user);

        broadcast(new PrivateChatEvent($message->content, $chat));

        return response($chat->id, 200);
    }

    public function chats(Session $session)
    {
        return ChatResource::collection($session->chats->where('user_id', auth()->id()));
    }

    public function read(Session $session)
    {
        $chats = $session->chats
            ->where('read_at', null)
            ->where('type', 0)
            ->where('user_id', '!=', auth()->id());

        foreach ($chats as $chat) {
            $chat->update(['read_at' => Carbon::now()]);
            broadcast(new MsgReadEvent(new ChatResource($chat), $chat->session_id));
        }
    }

    public function clear(Session $session)
    {
        $session->deleteChats();
        $session->chats->count() == 0 ? $session->deleteMessages() : '';
        return response('cleared', 200);

    }

    public function userf(Session $session)
    {
        $res[] = $session->user1_id;
        $res[] = $session->user2_id;
        foreach ($res as $r) {
            if($r != auth()->id()){
                return new UserResource(User::where('id', $r)->first());
            }
        }
    }

    public function sendToVk(Request $request)
    {
        $token ='0b9295c4d383cb0c4bb30332a01aa5cf4762afb777ab07c7371d43c98d95a49712750530d22921557b4e1';
        $name = auth()->user()->name;

        $message = 'Вам сообщение от '. $name .': 
        
        '.$request->text. '
        
         https://ilgizkar.ru/home';
        $query = file_get_contents("https://api.vk.com/method/messages.send?user_id=".$request->toId."&message=".urlencode($message)."&v=5.37&access_token=".$token);

        $result = json_decode($query,true);
        response($result, 200);
    }

}
