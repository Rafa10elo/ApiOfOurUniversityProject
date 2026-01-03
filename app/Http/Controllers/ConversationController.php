<?php

namespace App\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Resources\ConversationResource;
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

        $conversations->load('users');
        return ApiHelper::success("those are the conversations" , ConversationResource::collection($conversations));

    }

    public function store(StoreConversationRequest $request)
    {

        $conversation = Conversation::where('type','private')
            ->whereHas('users', fn ($q) => $q->where('users.id', auth()->id()))
           ->whereHas('users', fn ($q) => $q->where('users.id', $request->user_id))
            ->first();

        if ($conversation) {
            $conversation->load('users');
            return ApiHelper::success("conversation created",new ConversationResource($conversation));
        }

        $conversation = Conversation::create(['type' => 'private']);

        $conversation->users()->attach([
            auth()->id(),
          $request->user_id
        ]);

        $conversation->load('users');
        return ApiHelper::success("conversation created",new ConversationResource($conversation));
    }


    public function show($id)
    {
        $conversation = Conversation::whereHas('users', function ($q) {
            $q->where('users.id', auth()->id());
        })
            ->with('users:id,first_name')
            ->findOrFail($id);


        $conversation->load('users');
        return ApiHelper::success("this is the conversation" , new ConversationResource($conversation));
    }
}
