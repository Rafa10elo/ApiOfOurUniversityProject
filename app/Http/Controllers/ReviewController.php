<?php

namespace App\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Apartment;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, $apartmentId)
    {
        $apartment = Apartment::findOrFail($apartmentId);
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['apartment_id'] = $apartmentId;

        $review = Review::updateOrCreate(
            ['user_id' => auth()->id(), 'apartment_id' => $apartmentId],
            $data
        );

        return ApiHelper::success("Review submitted", new ReviewResource($review), 201);
    }

    public function index($apartmentId)
    {
        $apartment = Apartment::with('reviews.user')->findOrFail($apartmentId);
        return ApiHelper::success("Apartment reviews", ReviewResource::collection($apartment->reviews));
    }
}
