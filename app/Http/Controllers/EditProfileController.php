<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileImageRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Support\Facades\Hash;

class EditProfileController extends Controller
{
    public function updateProfileImage(UpdateProfileImageRequest $request)
    {
        $user = auth()->user();

        $user->clearMediaCollection('profile_image');
        $user->addMedia($request->file('image'))->toMediaCollection('profile_image');

        return response()->json([
            'message' => 'Profile image updated successfully.',
            'image_url' => $user->getFirstMediaUrl('profile_image'),
        ], 200);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
              return response()->json([

                'message' => 'Current password is incorrect.'
            ], 422);
        }

        $user->update(

            ['password' => Hash::make($request->new_password),]
        );

        return response()->json([

            'message' => 'Password updated successfully.',
        ], 200);
    }
}
