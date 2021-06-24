<?php

namespace App\Http\Controllers\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\TableResource;
use App\Http\Resources\UserResource;
use App\Services\BusinessTableService;
use App\Traits\FuncionAuthorizable;
use App\Traits\Responsable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Generator;

/**
 * Class TableController
 * @property BusinessTableService table
 * @package App\Http\Controllers\V1\Owner
 */
class TableController extends Controller
{
    use Responsable, FuncionAuthorizable;

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
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $table = $this->table->getRepo()->find($id, $this->table->getRepo()->getSelfRelations());
            $this->authorize(__FUNCTION__, $table);
            return $this->successResponse(new TableResource($table));
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
        $model = $this->table->getRepo()->getModel();

        try {
            $model->validate($request->all(), __FUNCTION__);

            $model->getConnectionResolver()->transaction(function () use ($request, &$table) {
                $table = $this->table->createTable($request->all());
                $this->authorize('create', $table);
            });

            return $this->successResponse(
                new TableResource($table),
                'Столиук Успешно создано',
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
        $model = $this->table->getRepo()->getModel();

        try {
            $model->validate($request->all(), __FUNCTION__);

            $model->getConnectionResolver()->transaction(function () use ($request, &$table) {
                $table = $this->table->getRepo()->updateAndGiveSelf(
                    $request->get('id'),
                    $request->all()
                );
                $this->authorize('update', $table);
            });

            return $this->successResponse($table, 'Столик Успешно Обновлено');
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
        $resolver = $this->table->getRepo()->getModel()->getConnectionResolver();
        $function = __FUNCTION__;
        try {
            $resolver->transaction(function () use ($request, $function) {
                $selected = $request->get('selected');
                $tables = $this->table->getRepo()->find($selected, ['qrcode']);
                $this->getAuthorizeFunction($function, $tables);
                $this->table->deleteTable($tables);
            });
            return $this->successResponse([], null, 'removed');
        } catch (AuthorizationException $e) {
            return $this->permissionErrorResponse();
        }
    }
}
