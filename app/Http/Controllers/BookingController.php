<?php

namespace App\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Apartment;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{

    public function store(StoreBookingRequest $request, $apartmentId)
    {
        $data = $request->validated();

        $apartment = Apartment::findOrFail($apartmentId);

        if ($apartment->owner_id == auth()->id()) {
            return ApiHelper::error("You cannot book your own apartment", 400);
        }

        $conflict = Booking::where('apartment_id', $apartmentId)
            ->where('status', 'approved')
            ->where(function ($q) use ($request) {
             $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                 ->orWhereBetween('end_date',   [$request->start_date, $request->end_date])
                ->orWhere(function ($q2) use ($request) {
                     $q2->where('start_date', '<=', $request->start_date)
               ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->exists();

        if ($conflict) {
            return ApiHelper::error("This date range is already booked", 409);
        }

        $booking = Booking::create([
            'user_id'      => auth()->id(),
            'apartment_id' => $apartmentId,
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
        ]);

        $apartment->owner->notify(new \App\Notifications\NewBookingNotification($booking));

        return ApiHelper::success("Booking request sent", new BookingResource($booking), 201);
    }


    public function approve($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->apartment->owner_id !== auth()->id()) {
            return ApiHelper::error("Not authorized", 403);
        }

        $conflict = Booking::where('apartment_id', $booking->apartment_id)
            ->where('id', '!=', $booking->id)
            ->where('status', 'approved')
            ->where(function ($q) use ($booking) {
                $q->whereBetween('start_date', [$booking->start_date, $booking->end_date])
                    ->orWhereBetween('end_date',   [$booking->start_date, $booking->end_date])
                    ->orWhere(function ($q2) use ($booking) {
                        $q2->where('start_date', '<=', $booking->start_date)
                            ->where('end_date', '>=', $booking->end_date);
                    });
            })
            ->exists();

        if ($conflict) {
            return ApiHelper::error("This booking conflicts with an existing approved booking", 409);
        }

        $booking->update(['status' => 'approved']);

        $booking->user->notify(new \App\Notifications\BookingStatusNotification($booking));

        return ApiHelper::success("Booking approved", new BookingResource($booking));
    }


    public function reject($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->apartment->owner_id !== auth()->id()) {
            return ApiHelper::error("Not authorized", 403);
        }

        $booking->update(['status' => 'rejected']);

        $booking->user->notify(new \App\Notifications\BookingStatusNotification($booking));

        return ApiHelper::success("Booking rejected", new BookingResource($booking));
    }
    public function update(UpdateBookingRequest $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $user = auth()->user();

        if ($request->has('status')) {
            if ($booking->apartment->owner_id !== $user->id) {
                return ApiHelper::error("Not authorized to change status", 403);
            }
        }

        if ($request->hasAny(['start_date', 'end_date'])) {
            if ($booking->user_id !== $user->id) {
                return ApiHelper::error("Not authorized to change booking dates", 403);
            }

            $start = $request->start_date ?? $booking->start_date;
            $end   = $request->end_date ?? $booking->end_date;

            $conflict = Booking::where('apartment_id', $booking->apartment_id)
                ->where('id', '!=', $booking->id)
                ->where('status', 'approved')
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_date', [$start, $end])
                        ->orWhereBetween('end_date', [$start, $end])
                        ->orWhere(function ($q2) use ($start, $end) {
                            $q2->where('start_date', '<=', $start)
                                ->where('end_date', '>=', $end);
                        });
                })
                ->exists();

            if ($conflict) {
                return ApiHelper::error("This date range conflicts with an existing approved booking", 409);
            }
        }

        $booking->update($request->validated());

        if ($request->has('status')) {
            $booking->user->notify(new \App\Notifications\BookingStatusNotification($booking));
        }

        return ApiHelper::success("Booking updated", new \App\Http\Resources\BookingResource($booking));
    }
}
