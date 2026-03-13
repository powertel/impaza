<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    public string $title;
    public string $body;
    public array $payload;

    public function __construct(string $title, string $body, array $payload = [])
    {
        $this->title = $title;
        $this->body = $body;
        $this->payload = $payload;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'payload' => $this->payload,
        ];
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
