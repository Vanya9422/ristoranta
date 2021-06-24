<?php

namespace App\Http\Controllers\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\FuncionAuthorizable;
use App\Traits\Responsable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class WorkersController
 * @property UserService worker
 * @package App\Http\Controllers\V1\Owner
 */
class WorkersController extends Controller
{
    use Responsable, FuncionAuthorizable;

    /**
     * @var UserService
     */
    private UserService $worker;

    /**
     * BusinessController constructor.
     * @param UserService $worker
     */
    public function __construct(UserService $worker)
    {
        $this->worker = $worker;
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $worker = $this->worker->getRepo()->find(
                $id,
                $this->worker->getRepo()->getSelfRelations()
            );

            $this->authorize(__FUNCTION__, $worker);
            return $this->successResponse(new UserResource($worker));
        } catch (AuthorizationException $e) {
            return $this->permissionErrorResponse();
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $model = $this->worker->getRepo()->getModel();
        $function = __FUNCTION__;

        try {
            $model->validate($request->all(), $function);
            $model->getConnectionResolver()->transaction(function () use ($request, &$worker, $function) {
                $worker = $this->worker->createOrUpdateBusinessUser($request->all());
                $this->authorize($function, $worker);
            });
            return $this->successResponse(new UserResource($worker), 'Работник Успешно создано', 'created');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (AuthorizationException $e) {
            return $this->permissionErrorResponse();
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $model = $this->worker->getRepo()->getModel();
        $function = __FUNCTION__;

        try {
            $model->validate($request->all(), $function);

            $model->getConnectionResolver()->transaction(function () use ($request, &$worker, $function) {
                $worker = $this->worker->createOrUpdateBusinessUser($request->all());
                $this->authorize($function, $worker);
            });

            return $this->successResponse(
                new UserResource($worker), 'Работник Успешно Обновлено'
            );
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (AuthorizationException $e) {
            return $this->permissionErrorResponse();
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $model = $this->worker->getRepo()->getModel();
        $function = __FUNCTION__;
        try {
            $model->getConnectionResolver()->transaction(function () use ($request, $function) {
                $selected = $request->get('selected');
                $workers = $this->worker->getRepo()->find($selected);
                $this->getAuthorizeFunction($function, $workers);
                $this->worker->getRepo()->deleteModel($workers);
            });
            return $this->successResponse([], null, 'removed');
        } catch (AuthorizationException $e) {
            return $this->permissionErrorResponse();
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->getMessage());
        }
    }
}
