<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
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

        return response()->json($messages);
    }


    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required|string'
        ]);

        $isMember = \DB::table('conversation_user')
            ->where('conversation_id', $request->conversation_id)
            ->where('user_id', auth()->id())
          ->exists();

        if (! $isMember) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
         'sender_id' => auth()->id(),
            'message' => $request->message
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message, 201);
    }
}
