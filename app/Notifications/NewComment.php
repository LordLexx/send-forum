<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewComment extends Notification
{
    use Queueable;

    public $post;
    public $commenter;

    public function __construct($post, $commenter)
    {
        $this->post = $post;
        $this->commenter = $commenter;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->commenter->name . ' commented on your post: ' . $this->post->title,
            'url' => route('posts.show', $this->post)
        ];
    }
}
