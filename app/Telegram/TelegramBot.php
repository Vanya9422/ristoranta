<?php

namespace App\Telegram;

use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Objects\WebhookInfo;
use Vinkla\Hashids\Facades\Hashids;

class TelegramBot
{
    /**
     * @var Api
     */
    private Api $telegram;

    /**
     * TelegramBot constructor.
     */
    public function __construct()
    {
        $this->telegram = new Api();
    }

    /**
     * @return bool
     * @throws TelegramSDKException
     */
    public function setWebHook(): bool
    {
        $ip = ipImplode(env("TELEGRAM_BOT_IP"));
        $hash = Hashids::connection('telegram')->encode($ip);
        return $this->telegram->setWebhook(['url' => env('TELEGRAM_WEBHOOK_URL') . $hash]);
    }

    /**
     * @return bool
     * @throws TelegramSDKException
     */
    public function removeWebHook(): bool
    {
        return $this->telegram->removeWebhook();
    }

    /**
     * @return WebhookInfo
     * @throws TelegramSDKException
     */
    public function getWebHook(): WebhookInfo
    {
        return $this->telegram->getWebhookInfo();
    }

    /**
     * @param $subject
     * @param $chat_id
     * @param $numberTale
     * @param string|null $comment
     * @return void
     * @throws TelegramSDKException
     */
    public function sendNotification($subject, $chat_id, $numberTale, ?string $comment = ''): void
    {
        $message = "$subject \n" . "N# $numberTale \n\n" . $comment;
        $this->telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => $message
        ]);
    }

    /**
     * @return Update
     */
    public function updates(): Update
    {
        return $this->telegram->getWebhookUpdate();
    }

    /**
     * @param $chat_id
     * @param $message
     * @return void
     * @throws TelegramSDKException
     */
    public function answerMessage($chat_id, $message): void
    {
        $this->telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => $message
        ]);
    }
}
