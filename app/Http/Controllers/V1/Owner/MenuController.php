<?php

namespace App\Http\Controllers\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Services\BusinessMenuService;
use App\Traits\FuncionAuthorizable;
use App\Traits\Responsable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class MenuController
 * @property BusinessMenuService business
 * @package App\Http\Controllers\V1\Owner
 */
class MenuController extends Controller
{
    use Responsable, FuncionAuthorizable;

    /**
     * @var BusinessMenuService
     */
    private BusinessMenuService $business;

    /**
     * BusinessController constructor.
     * @param BusinessMenuService $business
     */
    public function __construct(BusinessMenuService $business)
    {
        $this->business = $business;
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function index(Request $request, $id)
    {
        $dish_id = $request->get('dish_id');
        $perPage = $request->get('perPage');
        $dishes = $this->business->getRepo()->getBusinessDishes($id, $dish_id, $perPage);

        return $dishes instanceof Collection
            ? MenuResource::collection($dishes)
            : (new MenuResource($dishes));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $model = $this->business->getRepo();

        $function = __FUNCTION__;
        try {
            $model->getModel()->validate($request->all(), $function);
            $model->getModel()->getConnectionResolver()->transaction(function () use (
                $request, $function, &$dish
            ) {
                $dish = $this->business->createDish($request);
            });

            return $this->successResponse(
                (new MenuResource($dish)),
                'Блюдо Успешно создано',
                'created'
            );
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function block(Request $request): JsonResponse
    {
        $model = $this->business->getRepo();
        try {
            $model->getModel()->validate($request->all(), __FUNCTION__);
            $this->business->blockDish($request->all());

            $action = $request->get('action') == 'block' ? 'Заблокированна' : 'Разблокированна';
            return $this->successResponse([], 'Блюдо Успешно ' . $action);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $model = $this->business->getRepo();

        $function = __FUNCTION__;
        try {
            $model->getModel()->validate($request->all(), $function);

            $model->getModel()->getConnectionResolver()->transaction(function () use (
                $request, &$dish, $function
            ) {
                $dish = $this->business->updateDish($request);
            });

            return $this->successResponse(
                [], 'Блюдо Успешно Обновлено'
            );
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $this->business->deleteDish($id, $request->get('force') ?? false);
            return $this->successResponse([], null, 'removed');
        } catch (ModelNotFoundException $e) {
            return $this->validationErrorResponse($e->getMessage());
        }
    }
}
