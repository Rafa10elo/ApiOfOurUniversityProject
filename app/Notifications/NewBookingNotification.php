<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewBookingNotification extends Notification implements ShouldBroadcast
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
            'message' => "New booking request for {$this->booking->apartment->title}",
            'booking_id' => $this->booking->id,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "New booking request for {$this->booking->apartment->title}",
            'booking_id' => $this->booking->id,
        ]);
    }
}
