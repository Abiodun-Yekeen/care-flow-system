<?php

namespace App\Notifications;

use App\Modules\OfficeFiles\File\Models\File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FileSubmittedToHOD extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public File $file, public String $data) {}

    public function via($notifiable): array
    {
        // database stores it for the "Bell" icon, broadcast makes it real-time
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable): array
    {
        return [
            'action_type' =>  $this->data,
            'file_id' => $this->file->id,
            'subject' => $this->file->subject,
            'message' => "New file received: {$this->file->subject}",
            'priority' => $this->file->priority,
            'sender' => $notifiable->name,
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
