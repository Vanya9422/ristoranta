<?php

namespace App\Utils;

use Illuminate\Support\Facades\Http;

class TelegramBot
{
    /**
     * @var string $token
     */
    private static string $token = "1623504607:AAFN28g5tlbKZH9DJDF8hLeqQXZ7mp4SpTc";

    /**
     * @param $subject
     * @param $chat_id
     * @param $numberTale
     * @param null|string $comment
     * @return void
     */
    public static function sendNotification($subject, $chat_id, $numberTale, $comment = ''): void
    {
        $message = "$subject \n" . "N# $numberTale \n\n" . $comment;
        $token = self::$token;
        Http::get("https://api.telegram.org/bot$token/sendMessage", [
            'chat_id' => $chat_id,
            'parse_mode' => 'markdown',
            'text' => $message
        ]);
    }
}
