<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewResource extends Notification
{
    use Queueable;

    public function resource;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'New resource added: ' . $this->resource->title, // adjust this if your column name is different
            'url' => route('resources')
        ];
    }
}
