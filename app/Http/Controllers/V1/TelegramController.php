<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\User\UserInterface;
use App\Telegram\TelegramBot;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class TelegramController
 * @property TelegramBot bot
 * @property UserInterface user
 * @package App\Http\Controllers\V1
 */
class TelegramController extends Controller
{
    /**
     * @var TelegramBot
     */
    private TelegramBot $bot;

    /**
     * @var UserInterface
     */
    private UserInterface $user;

    /**
     * TelegramController constructor.
     * @param TelegramBot $bot
     * @param UserInterface $user
     */
    public function __construct(TelegramBot $bot, UserInterface $user)
    {
        $this->bot = $bot;
        $this->user = $user;
    }

    /**
     * Ответ когда код был неверно
     */
    const INCORRECTANSWER = 'Kод был введен неправильно';

    /**
     * Ответ когда код был правильно
     */
    const CORRECTANSWER = 'Личный кабинет успешно подключен к боту';

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws TelegramSDKException
     */
    public function getBotUpdates(Request $request): JsonResponse
    {
        $chat_id = $this->bot->updates()->getMessage()->get('chat')->get('id');
        $textMessage = $this->bot->updates()->getMessage()->get('text');

        if ($this->bot->updates()->getMessage()->has('entities')) {
            if (strpos($textMessage, '/start ') !== false) {
                $textMessage = explode(' ', $textMessage)[1];
            }
        }

        try {
            $hash = Hashids::decode($textMessage);

            if (empty($hash)) {
                $this->bot->answerMessage($chat_id, self::INCORRECTANSWER);
                return response()->json('Error', 200);
            }

            $this->user->update($hash[0], ['chat_id' => $chat_id]);
            $this->bot->answerMessage($chat_id, self::CORRECTANSWER);

            return response()->json('Success Response', 200);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            $this->bot->answerMessage($chat_id, self::INCORRECTANSWER);
            return response()->json('Model not fount', 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json('Unprocessable entity', 200);
        }
    }
}
