<?php

namespace App\Http\Controllers\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Repositories\Eloquent\Business\CategoryInterface;
use App\Traits\FuncionAuthorizable;
use App\Traits\Responsable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class CategoryController
 * @property CategoryInterface category
 * @package App\Http\Controllers\V1\Owner
 */
class CategoryController extends Controller
{
    use Responsable, FuncionAuthorizable;

    /**
     * @var CategoryInterface
     */
    private CategoryInterface $category;

    /**
     * CategoryController constructor.
     * @param CategoryInterface $category
     */
    public function __construct(CategoryInterface $category)
    {
        $this->category = $category;
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function index(Request $request, $id): JsonResponse
    {
        $category_id = $request->get('category_id');

        $categories = $this->category->getBusinessCategories($id, $category_id);

        $categories = $categories instanceof Collection
            ? CategoryResource::collection($categories)
            : (new CategoryResource($categories));

        return $this->successResponse($categories);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $this->category->getModel()->validate($request->all(), __FUNCTION__);

            $category = $this->category->create($request->all());

            return $this->successResponse(
                new CategoryResource($category),
                'Катагория Успешно создано',
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
        try {
            $this->category->getModel()->validate($request->all(), __FUNCTION__);

            $category = $this->category->updateAndGiveSelf(
                $request->get('id'),
                $request->all()
            );

            return $this->successResponse(new CategoryResource($category), 'Катагория Успешно Обновлено');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (AuthorizationException $e) {
            return $this->permissionErrorResponse();
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
            $this->category->firstAndDelete($id, $request->get('force') ?? false);
            return $this->successResponse([], null, 'removed');
        } catch (ModelNotFoundException $e) {
            return $this->validationErrorResponse($e->getMessage());
        }
    }
}
