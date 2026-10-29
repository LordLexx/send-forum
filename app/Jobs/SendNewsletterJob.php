<?php

namespace App\Jobs;

use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $newsletterId,
        public array $recipients
    ) {}

    public function handle(): void
    {
        $newsletter = Newsletter::find($this->newsletterId);

        if (!$newsletter) return;

        foreach ($this->recipients as $email) {
            Mail::to($email)->send(new NewsletterMail($newsletter, $email));

            // Pause for 0.6 seconds to avoid Mailtrap's "2 emails per second" limit
            usleep(600000);
        }
    }
}
