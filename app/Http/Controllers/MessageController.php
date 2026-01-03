<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Helpers\ApiHelper;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index($conversationId)
    {
        $conversation = Conversation::whereHas('users', function ($q) {
            $q->where('users.id', auth()->id());
        })->findOrFail($conversationId);

        $messages = Message::where('conversation_id', $conversationId)
            ->with('sender:id,first_name')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return ApiHelper::success("Those are the messages of the conversation",messageResource::collection($messages));
    }

    public function store(StoreMessageRequest $request, $conversationId)
    {

        $isMember = \DB::table('conversation_user')
            ->where('conversation_id', $conversationId)
            ->where('user_id', auth()->id())
            ->exists();

        if (! $isMember) {
            return ApiHelper::error("You can't send message to this convo", 401);
        }

        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => auth()->id(),
            'message' => $request->message
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return ApiHelper::success('message sent' , new MessageResource($message));
    }

}
