<?php

namespace App\Events;

use App\Modules\OfficeFiles\File\Models\File;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $file;
    public $userId;

    public function __construct(File $file, $userId) {
        $this->file = $file;
        $this->userId = $userId;
    }

    public function broadcastOn() {
        return new PrivateChannel('App.Modules.Core.Iam.Models.User.' . $this->userId);
    }


}
