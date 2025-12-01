<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Helpers\ApiHelper;
use Dedoc\Scramble\Attributes\Authenticated;

class AdminController extends Controller
{

    public function pendingUsers()
    {
        $users = User::where('verification_state', 'pending')->get();
        return ApiHelper::success(null, UserResource::collection($users));
    }


    public function verifyUser($id)
    {
        $user = User::findOrFail($id);
        $user->verification_state = 'verified';
        $user->save();

        return ApiHelper::success("User verified", new UserResource($user));
    }


    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $user->verification_state = 'rejected';
        $user->save();

        return ApiHelper::success("User rejected", new UserResource($user));
    }
}
