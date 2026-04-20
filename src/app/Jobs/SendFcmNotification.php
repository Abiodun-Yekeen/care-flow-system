<?php

namespace App\Jobs;

use App\Modules\Core\Shared\Services\Firebase\FcmService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendFcmNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public $user,
        public $title,
        public $body,
        public $data = []
    ) {}

    public function handle(FcmService $fcm)
    {
        foreach ($this->user->fcmTokens as $token) {
            $fcm->send(
                $token->token,
                $this->title,
                $this->body,
                $this->data
            );
        }
    }
}
