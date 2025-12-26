<?php

namespace App\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Apartment;
use App\Models\Booking;


class BookingController extends Controller
{

    public function store(StoreBookingRequest $request, $apartmentId)
    {
        $data = $request->validated();

        $apartment = Apartment::findOrFail($apartmentId);

        if ($apartment->owner_id == auth()->id())
            return ApiHelper::error("You cannot book your own apartment", 400);


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

        if ($conflict)
            return ApiHelper::error("This date range is already booked", 409);


        $booking = Booking::create([
            'user_id'      => auth()->id(),
            'apartment_id' => $apartmentId,
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'status' => 'pending',

        ]);

        $apartment->owner->notify(new \App\Notifications\NewBookingNotification($booking));

        return ApiHelper::success("booking request sent", new BookingResource($booking->load(['user','apartment'])), 201);
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
            return ApiHelper::error("this booking conflicts with an existing approved booking", 410);
        }

        $booking->update(['status' => 'approved']);

        $booking->user->notify(new \App\Notifications\BookingStatusNotification($booking));

        return ApiHelper::success("booking approved", new BookingResource($booking->load(['user','apartment'])));
    }


    public function reject($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->apartment->owner_id !== auth()->id()) {
            return ApiHelper::error("not authorized", 403);
        }

        $booking->update(['status' => 'rejected']);

        $booking->user->notify(new \App\Notifications\BookingStatusNotification($booking));

        return ApiHelper::success("Booking rejected", new BookingResource($booking->load(['user','apartment'])));
    }


    public function ownerPending()
    {
        $bookings = Booking::with(['user','apartment'])
            ->whereHas('apartment', fn($q) =>
        $q->where('owner_id', auth()->id())
        )->where('status', 'pending')->get();

        return ApiHelper::success(
            "owner pending bookings",
            BookingResource::collection($bookings)
        );
    }

    public function ownerApproved()
    {
        $bookings = Booking::with(['user','apartment'])
            ->whereHas('apartment', fn($q) =>
        $q->where('owner_id', auth()->id())
        )->where('status', 'approved')
            ->where('end_date', '>=', date('Y-m-d'))
            ->get();

        return ApiHelper::success(
            "Owner active bookings",
            BookingResource::collection($bookings)
        );
    }

    public function ownerCancelled()
    {
        $bookings = Booking::with(['user','apartment'])
            ->whereHas('apartment', fn($q) =>
        $q->where('owner_id', auth()->id())
        )->whereIn('status', ['cancelled','rejected'])->get();

        return ApiHelper::success(
            "Owner cancelled bookings",
            BookingResource::collection($bookings)
        );
    }

    public function ownerPast()
    {
        $bookings = Booking::with(['user','apartment'])
            ->whereHas('apartment', fn($q) =>
        $q->where('owner_id', auth()->id())
         )->where('status','approved')
             ->where('end_date','<',date('Y-m-d'))
            ->get();

        return ApiHelper::success(
            "Owner past bookings",
              BookingResource::collection($bookings)
        );
    }



    public function renterPending()
    {
        $bookings = Booking::with(['user','apartment'])
            ->where('user_id', auth()->id())
            ->where('status','pending')->get();

            return ApiHelper::success(
            "My pending bookings",
            BookingResource::collection($bookings)
        );
    }

    public function renterApproved()
    {
        $bookings = Booking::with(['user','apartment'])
            ->where('user_id', auth()->id())
            ->where('status','approved')
            ->where('end_date','>=',date('Y-m-d'))
            ->get();

           return ApiHelper::success(
            "My active bookings",
            BookingResource::collection($bookings)
        );
    }

    public function renterCancelled()
    {
        $bookings = Booking::with(['user','apartment'])
            ->where('user_id', auth()->id())
            ->whereIn('status',['cancelled','rejected'])->get();

        return ApiHelper::success(
            "My cancelled bookings",
            BookingResource::collection($bookings)
        );
    }

    public function renterPast()
    {
        $bookings = Booking::with(['user','apartment'])
            ->where('user_id', auth()->id())
            ->where('status','approved')
          ->where('end_date','<',date('Y-m-d'))
            ->get();

          return ApiHelper::success(
            "My past bookings",
            BookingResource::collection($bookings)
        );
    }



    public function apartmentCalendar($apartmentId)
    {
        $today = date('Y-m-d');

        $bookings = Booking::with(['user','apartment'])
            ->where('apartment_id', $apartmentId)
            ->select('start_date', 'end_date', 'status')
            ->get()
          ->map(function ($booking) use ($today) {
                $type = $booking->status;

                if ($booking->status === 'approved' && $booking->end_date < $today)
                    $type = 'past';

                return [
                    'start_date' => $booking->start_date,
                 'end_date'   => $booking->end_date,
                    'status'     => $type,
                ];
            });

        return ApiHelper::success(
            "Apartment calendar",$bookings
        );
    }
    public function update(UpdateBookingRequest $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $user = auth()->user();

        if ($booking->user_id !== $user->id)
            return ApiHelper::error("not authorized", 403);


        $start = $request->start_date ??$booking->start_date;
        $end   = $request->end_date   ??$booking->end_date;

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
            })->exists();

        if ($conflict)
            return ApiHelper::error("this date range is already booked",409);

        if($booking->status === 'approved')
        $booking->apartment->owner->notify(new \App\Notifications\BookingUpdatedNotification($booking));


        $booking->update([
            'start_date' => $start,
            'end_date'   => $end,
            'status'     => 'pending',
        ]);



        return ApiHelper::success(
            "booking updated..... waiting for owner approval",
            new BookingResource($booking->load(['user','apartment']))
        );
    }

    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);
        $user = auth()->user();

        if ($booking->user_id !== $user->id) return ApiHelper::error("not authorized", 403);


        if ($booking->status !== 'pending') return ApiHelper::error("only pending bookings can be cancelled", 400);


        $booking->update(['status' => 'cancelled']);

        return ApiHelper::success(
            "booking cancelled successfully",
            new BookingResource($booking->load(['user','apartment']))
        );
    }

}



