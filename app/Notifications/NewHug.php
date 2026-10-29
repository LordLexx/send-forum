<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewHug extends Notification
{
    use Queueable;

    public $post;
    public $hugger;

    public function __construct($post, $hugger)
    {
        $this->post = $post;
        $this->hugger = $hugger;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

        public function toArray($notifiable)
    {
        return [
            'message' => $this->hugger->name . ' sent a hug to your post: ' . $this->post->title,
            'url' => route('posts.show', $this->post)
        ];
    }
}
