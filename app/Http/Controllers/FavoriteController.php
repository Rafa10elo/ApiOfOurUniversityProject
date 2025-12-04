<?php

namespace App\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\Apartment;

class FavoriteController extends Controller
{
    public function toggle($apartmentId)
    {
        $user = auth()->user();
        $apartment = Apartment::findOrFail($apartmentId);

        if ($user->favorites()->where('apartment_id', $apartmentId)->exists()) {
            $user->favorites()->detach($apartmentId);
            return ApiHelper::success("Removed from favorites");
        }

        $user->favorites()->attach($apartmentId);
        return ApiHelper::success("Added to favorites");
    }

    public function myFavorites()
    {
        $user = auth()->user();
        $favorites = $user->favorites()->with('owner')->get();

        return ApiHelper::success("Favorites list", $favorites);
    }
}
