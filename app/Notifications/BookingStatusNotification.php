<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class BookingStatusNotification extends Notification implements ShouldBroadcast
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
            'message' => "Your booking was {$this->booking->status}",
            'booking_id' => $this->booking->id,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "Your booking was {$this->booking->status}",
            'booking_id' => $this->booking->id,
        ]);
    }
}
