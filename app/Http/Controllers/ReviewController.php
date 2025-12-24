<?php

namespace App\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Apartment;
use App\Models\Booking;
use App\Models\Review;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, $apartmentId)
    {
        $apartment = Apartment::findOrFail($apartmentId);

        $hb = Booking::where('user_id', auth()->id())
            ->where('apartment_id', $apartmentId)
           ->where('status', 'approved')
           ->where('end_date', '<', Carbon::today())
             ->exists();

        if (!$hb) {
            return ApiHelper::error(
                "you can only review apartments you have booked before",
                403
            );
        }

        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['apartment_id'] = $apartmentId;

        $review = Review::updateOrCreate(
            [
             'user_id' => auth()->id(),
             'apartment_id' => $apartmentId
            ],
            $data
        );

        return ApiHelper::success(
             "review submitted", new ReviewResource($review->load('user')), 201);
    }
    /**
     * @unauthenticated
     */
    public function index($apartmentId)
    {
        $apartment = Apartment::with('reviews.user')->findOrFail($apartmentId);
          return ApiHelper::success("Apartment reviews", ReviewResource::collection($apartment->reviews));
    }
}
