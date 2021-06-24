<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\TranslationResource;
use App\Repositories\Eloquent\Language\LanguageInterface;
use App\Services\TranslationService;
use App\Traits\Responsable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;

class LanguageController extends Controller
{
    use Responsable;

    /**
     * @var LanguageInterface
     */
    private LanguageInterface $lang;

    /**
     * TranslationController constructor.
     * @param LanguageInterface $lang
     */
    public function __construct(LanguageInterface $lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $languages = $this->lang->getModel()->all();
        return $this->successResponse(LanguageResource::collection($languages));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $this->lang->getModel()->validate($request->all(), __FUNCTION__);
            $language = $this->lang->create($request->all());

            return $this->successResponse(new LanguageResource($language),'Язык успешно создано', 'created');
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
            $this->lang->getModel()->validate($request->all(), __FUNCTION__);

            $this->lang->update($request->get('id'), $request->all());

            return $this->successResponse([], 'Язык успешно обновлено', 'created');
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
            $this->lang->firstAndDelete($id, $request->get('force') ?? false);
            return $this->successResponse([], null, 'removed');
        } catch (ModelNotFoundException $e) {
            return $this->validationErrorResponse($e->getMessage());
        }
    }
}
