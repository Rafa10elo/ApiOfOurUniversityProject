<?php


namespace App\Http\Controllers;

use App\Models\User;

class AdminViewController extends Controller
{
    public function pendingUsers()
    {
        $users = User::where('verification_state', 'pending')->get();
        return view('admin.pending-users', compact('users'));
    }
    public function verifiedUsers()
    {
        $users = User::where('verification_state', 'verified')->get();
        return view('admin.verified-users', compact('users'));
    }

    public function rejectedUsers()
    {
        $users = User::where('verification_state', 'rejected')->get();
        return view('admin.rejected-users', compact('users'));
    }

}
