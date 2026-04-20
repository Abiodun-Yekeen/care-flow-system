<?php

namespace App\Modules\Core\Shared\Services\Firebase;

use Google\Client;
use Illuminate\Support\Facades\Http;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\ApnsConfig;
//class FcmService
//{
//    protected $messaging;
//
//    public function __construct()
//    {
//        $this->messaging = (new \Kreait\Firebase\Factory)
//            ->withServiceAccount(storage_path('app/careflow-83b22-firebase-adminsdk-fbsvc-f9c4d19efd.json'))
//            ->createMessaging();
//    }
//
//    public function send($token, $title, $body, $data = [])
//    {
//        $message = CloudMessage::new()
//            ->withNotification([
//                'title' => $title,
//                'body' => $body,
//            ])
//            ->withData($data);
//
//        return $this->messaging->send($message, $token);
//    }
//}


class FcmService
{
    private function getAccessToken()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/careflow-83b22-firebase-adminsdk-fbsvc-f9c4d19efd.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        return $client->fetchAccessTokenWithAssertion()['access_token'];
    }

    public function send($token, $title, $body, $data = [])
    {
        $accessToken = $this->getAccessToken();

        $projectId = config('services.firebase.project_id');

        $response = Http::withToken($accessToken)
            ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                "message" => [
                    "token" => $token,
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
                    ],
                    "data" => array_map('strval', $data), // IMPORTANT FIX
                ]
            ]);

        if (!$response->successful()) {
            logger()->error('FCM FAILED', [
                'response' => $response->body(),
                'token' => $token,
            ]);
        }

        return $response->json();
    }
}








//class FcmService
//{
//    public function send($token, $title, $body, $data = [])
//    {
//        $client = new Client();
//        $client->setAuthConfig(storage_path('app/careflow-83b22-firebase-adminsdk-fbsvc-f9c4d19efd.json'));
//        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
//
//        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];
//
//        return Http::withToken($accessToken)
//            ->post("https://fcm.googleapis.com/v1/projects/" . config('services.firebase.project_id') . "/messages:send", [
//                "message" => [
//                    "token" => $token,
//                    "notification" => [
//                        "title" => $title,
//                        "body" => $body
//                    ],
//                    "data" => $data
//                ]
//            ])->json();
//    }
//}
