<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{

    public function index()
    {
        $conversations = Conversation::whereHas('users', function ($q) {
            $q->where('users.id', auth()->id());
        })
            ->with(['users:id,first_name'])
           ->latest()
            ->get();

        return response()->json($conversations);
    }

    public function store(Request $request)
    {
        $request->validate([
          'user_id' => 'required|exists:users,id'
        ]);

        $conversation = Conversation::where('type', 'private')
            ->whereHas('users', fn ($q) => $q->where('users.id', auth()->id()))
           ->whereHas('users', fn ($q) => $q->where('users.id', $request->user_id))
            ->first();

        if ($conversation) {
            return response()->json($conversation);
        }

        $conversation = Conversation::create(['type' => 'private']);

        $conversation->users()->attach([
            auth()->id(),
          $request->user_id
        ]);

        return response()->json($conversation, 201);
    }


    public function show($id)
    {
        $conversation = Conversation::whereHas('users', function ($q) {
            $q->where('users.id', auth()->id());
        })
            ->with('users:id,first_name')
            ->findOrFail($id);

        return response()->json($conversation);
    }
}
