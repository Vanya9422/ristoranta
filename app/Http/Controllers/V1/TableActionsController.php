<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\BusinessTableService;
use App\Traits\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Telegram\Bot\Exceptions\TelegramSDKException;

/**
 * Class TableActionsController
 * @property BusinessTableService table
 * @package App\Http\Controllers\V1
 */
class TableActionsController extends Controller
{
    use Responsable;

    const MENU = 'Принести меню';
    const BILL = 'Принести счёт';
    const OTHER = 'Подойдите пожалуйста';

    /**
     * @var BusinessTableService
     */
    private BusinessTableService $table;

    /**
     * BusinessController constructor.
     * @param BusinessTableService $table
     */
    public function __construct(BusinessTableService $table)
    {
        $this->table = $table;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws TelegramSDKException
     */
    public function askWaiter(Request $request): JsonResponse
    {
        try {
            $this->table->getRepo()->getModel()->validate($request->all(), 'guest');

            $number = $this->table->getRepo()->find($request->get('table_id'))->number;

            $this->table->bot()->sendNotification(self::MENU,
                $request->get('chat_id'),
                $number,
                $request->get('comment')
            );

            return $this->successResponse();
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws TelegramSDKException
     */
    public function billMoney(Request $request): JsonResponse
    {
        try {
            $this->table->getRepo()->getModel()->validate($request->all(), 'guest');

            $number = $this->table->getRepo()->find($request->get('table_id'))->number;

            $this->table->bot()->sendNotification(self::BILL,
                $request->get('chat_id'),
                $number,
                $request->get('comment')
            );

            return $this->successResponse();
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws TelegramSDKException
     */
    public function getOther(Request $request): JsonResponse
    {
        try {
            $this->table->getRepo()->getModel()->validate($request->all(), 'guest');

            $number = $this->table->getRepo()->find($request->get('table_id'))->number;

            $this->table->bot()->sendNotification(self::OTHER,
                $request->get('chat_id'),
                $number,
                $request->get('comment')
            );

            return $this->successResponse();
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createReviewTable(Request $request): JsonResponse
    {
        try {
            $this->table->getRepo()->getModel()->validate($request->all(), 'review');

            $this->table->getRepo()->createReview(
                $request->get('table_id'),
                $request->get('review')
            );

            return $this->successResponse([], null, 'created');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createReviewBusiness(Request $request): JsonResponse
    {
        try {
            $this->table->getRepo()->getModel()->validate($request->all(), 'review');

            $this->table->getRepo()->createBusinessReview(
                $request->get('table_id'),
                $request->except('table_id'),
            );

            return $this->successResponse([], null, 'created');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        }
    }
}
