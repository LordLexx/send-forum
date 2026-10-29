<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewEventEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Allow up to 10 minutes for large user bases
    public int $timeout = 600;

    public function __construct(
        public Event $event
    ) {}

    public function handle(): void
    {
        // Chunk through users to avoid memory issues on large datasets
        User::where('status', 'approved')->chunk(100, function ($users) {
            foreach ($users as $user) {
                Mail::send('emails.new-event', [
                    'event' => $this->event,
                    'user'  => $user,
                ], function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('New Event: ' . $this->event->title);
                });
            }
        });
    }
}
