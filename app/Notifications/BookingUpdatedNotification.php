<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class BookingUpdatedNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(public $booking) {}

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'A booking update request needs your approval',
            'booking_id' => $this->booking->id,
            'type' => 'booking_update',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => 'A booking update request needs your approval',
            'booking_id' => $this->booking->id,
            'type' => 'booking_update',
        ]);
    }
}
