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

        return redirect()
            ->back()
            ->with('success', "User {$user->first_name} {$user->last_name} has been verified successfully.");
    }

    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $user->verification_state = 'rejected';
        $user->save();

        return redirect()
            ->back()
            ->with('error', "User {$user->first_name} {$user->last_name} has been rejected.");
    }
    public function removeUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()
            ->route('admin.verified')
            ->with('success', 'User removed successfully');
    }

}
