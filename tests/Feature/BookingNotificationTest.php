<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Booking;
use App\Notifications\BookingStatusNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;


class BookingNotificationTest extends TestCase
{
    public function test_sends_booking_status_notification()
    {
        Notification::fake();

        $user = new \App\Models\User();
        $user->email = "test@example.com";

        $booking = new \stdClass();
        $booking->status = "approved";

        // Fake apartment
        $booking->apartment = new \stdClass();
        $booking->apartment->title = "Test Apartment";

        $user->notify(new \App\Notifications\BookingStatusNotification($booking));

        Notification::assertSentTo(
            [$user],
            \App\Notifications\BookingStatusNotification::class
        );
    }
}
