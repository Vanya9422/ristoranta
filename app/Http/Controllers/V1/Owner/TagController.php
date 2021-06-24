<?php

namespace App\Http\Controllers\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Repositories\Eloquent\Business\TagInterface;
use App\Traits\FuncionAuthorizable;
use App\Traits\Responsable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class TagController
 * @property TagInterface tag
 * @package App\Http\Controllers\V1\Owner
 */
class TagController extends Controller
{
    use Responsable, FuncionAuthorizable;

    /**
     * @var TagInterface
     */
    private TagInterface $tag;

    /**
     * TagController constructor.
     * @param TagInterface $tag
     */
    public function __construct(TagInterface $tag)
    {
        $this->tag = $tag;
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function index(Request $request, $id): JsonResponse
    {
        $tag_id = $request->get('tag_id');

        $tags = $this->tag->getDishTags($id, $tag_id);

        $tags = $tags instanceof Collection
            ? TagResource::collection($tags)
            : (new TagResource($tags));

        return $this->successResponse($tags);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $this->tag->getModel()->validate($request->all(), __FUNCTION__);
            $tag =  $this->tag->create($request->all());

            return $this->successResponse(
                new TagResource($tag),
                'Тег Успешно создано',
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
            $this->tag->getModel()->validate($request->all(), __FUNCTION__);

            $tag = $this->tag->updateAndGiveSelf(
                $request->get('id'),
                $request->all()
            );

            return $this->successResponse(new TagResource($tag), 'Таг Успешно Обновлено');
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
            $this->tag->firstAndDelete($id, $request->get('force') ?? false);
            return $this->successResponse([], null, 'removed');
        } catch (ModelNotFoundException $e) {
            return $this->validationErrorResponse($e->getMessage());
        }
    }
}
