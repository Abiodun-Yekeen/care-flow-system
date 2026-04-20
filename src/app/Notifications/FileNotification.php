<?php

namespace App\Notifications;

use App\Jobs\SendFcmNotification;
use App\Modules\OfficeFiles\File\Models\File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FileNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public function __construct(public File $file, public String $action) {
        $this->onQueue('notifications');
    }

    public function via($notifiable): array
    {
        return [
            'database',
            'broadcast',

        ];
    }

    public function toArray($notifiable): array
    {
        $data= [
            'action_type' =>  $this->action, // file_returned,file_routed,new_submission
            'file_id' => $this->file->id,
            'subject' => $this->file->subject,
            'message' => "New file received: {$this->file->subject}",
            'priority' => $this->file->priority,
            'sender' => auth()->user()->name,
        ];


        return $data;

    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }


}



