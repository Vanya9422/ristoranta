<?php

namespace App\Http\Controllers\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessResource;
use App\Http\Resources\BusinessTypeResource;
use App\Http\Resources\TableResource;
use App\Http\Resources\UserResource;
use App\Services\BusinessService;
use App\Traits\FuncionAuthorizable;
use App\Traits\Responsable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;

/**
 * Class BusinessController
 * @property BusinessService businessService
 * @package App\Http\Controllers\V1\Owner
 */
class BusinessController extends Controller
{
    use Responsable, FuncionAuthorizable;

    /**
     * @var BusinessService
     */
    private BusinessService $businessService;

    /**
     * BusinessController constructor.
     * @param BusinessService $businessService
     */
    public function __construct(BusinessService $businessService)
    {
        $this->businessService = $businessService;
    }

    /**
     * TODO to discuss with Ivan / $request->user()->id /
     * @param Request $request
     * @return JsonResponse
     */
    public function getBusinessesAndTypes(Request $request): JsonResponse
    {
        [$types, $generalBusinesses] = $this->businessService->getUserBusinessesAndTypes(
            $request->user()->id
        );

        return $this->successResponse([
            'types' => BusinessTypeResource::collection($types),
            'general_businesses' => BusinessResource::collection($generalBusinesses),
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getWorkers($id): JsonResponse
    {
        try {
            $business = $this->businessService->getRepo()->find($id);
            $this->authorize('hasAccess', $business);
            $workers = $this->businessService->getRepo()->getWorkers($business);
            return $this->successResponse([
                'result_count' => count($workers),
                'data' => UserResource::collection($workers),
            ]);
        } catch (AuthorizationException $e) {
            return $this->permissionErrorResponse();
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function addWorkers(Request $request, $id): JsonResponse
    {
        $model = $this->businessService->table()->getModel();

        try {
            $model->validate($request->all(), __FUNCTION__);
            $business = $this->businessService->getRepo()->find($id);

            $this->authorize('hasAccess', $business);
            $model->getConnectionResolver()->transaction(function () use ($request, &$tables) {
                $tables = $this->businessService->addWorkerToTable(
                    $request->get('user_id'),
                    $request->get('selected'),
                );
            });

            $tables = $tables instanceof Collection
                ? TableResource::collection($tables)
                : (new TableResource($tables));

            return $this->successResponse($tables, 'Действие прошла успешно');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (AuthorizationException $e) {
            return $this->permissionErrorResponse();
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getTables($id): JsonResponse
    {
        try {
            $business = $this->businessService->getRepo()->find($id);
            $this->authorize('hasAccess', $business);
            $tables = $this->businessService->table()->findByCriteria(['business_id' => $id], [], true);
            return $this->successResponse(TableResource::collection($tables));
        } catch (AuthorizationException $e) {
            return $this->permissionErrorResponse();
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getBusinesses(Request $request): JsonResponse
    {
        $businesses = $this->businessService->getRepo()->userBusinesses(
            $request->user()->id
        );

        return $this->successResponse([
            'result_count' => count($businesses),
            'data' => BusinessResource::collection($businesses),
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $businessRepo = $this->businessService->getRepo();
        $business = $businessRepo->find($id, $businessRepo->getSelfRelations());
        return $this->successResponse((new BusinessResource($business)));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $businessRepo = $this->businessService->getRepo();
        $function = __FUNCTION__;
        try {
            $businessRepo->getModel()->validate($request->all(), $function);

            $businessRepo->getModel()->getConnectionResolver()->transaction(function () use (
                $request, &$business, $businessRepo, $function
            ) {
                $business = $businessRepo->create(setUserInData($request));
                $this->authorize($function, $business);
            });

            return $this->successResponse(
                (new BusinessResource($business)),
                'Бизнес Успешно создано',
                'created'
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
    public function update(Request $request): JsonResponse
    {
        $businessRepo = $this->businessService->getRepo();
        $model = $businessRepo->getModel();

        try {
            $model->validate($request->all(), __FUNCTION__);

            $model->getConnectionResolver()->transaction(function () use ($request, &$business, $businessRepo) {
                $business = $businessRepo->updateAndGiveSelf(
                    $request->get('id'),
                    $request->all(),
                    $businessRepo->getSelfRelations()
                );
                $this->authorize('update', $business);
            });

            return $this->successResponse(
                (new BusinessResource($business)),
                'Бизнес Успешно Обновлено'
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
        $businessRepo = $this->businessService->getRepo();
        $resolver = $businessRepo->getModel()->getConnectionResolver();
        $function = __FUNCTION__;
        try {
            $resolver->transaction(function () use ($request, $businessRepo, $function) {
                $selected = $request->get('selected');
                $businesses = $businessRepo->find($selected);
                $this->getAuthorizeFunction($function, $businesses);
                $businessRepo->deleteModel($businesses);
            });
            return $this->successResponse([], null, 'removed');
        } catch (AuthorizationException $e) {
            return $this->permissionErrorResponse();
        }
    }
}
