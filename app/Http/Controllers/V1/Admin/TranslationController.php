<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TranslationResource;
use App\Services\TranslationService;
use App\Traits\Responsable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;

class TranslationController extends Controller
{
    use Responsable;

    /**
     * @var TranslationService
     */
    private TranslationService $trans;

    /**
     * TranslationController constructor.
     * @param TranslationService $translation
     */
    public function __construct(TranslationService $translation)
    {
        $this->trans = $translation;
    }

    /**
     * @param Request $request
     * @param $section
     * @return JsonResponse
     */
    public function show(Request $request, $section): JsonResponse
    {
        $language = $request->header('Accept-Language');

        $translations = $this->trans->getTranslations($section, [], $language);

        $translation = $translations instanceof Collection
            ? TranslationResource::collection($translations)
            : (new TranslationResource($translations));

        return $this->successResponse($translation);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $trans = $this->trans->getRepo();

        try {
            $trans->getModel()->validate($request->all(), __FUNCTION__);

            $translation = $trans->create($request->all());

            return $this->successResponse(
                (new TranslationResource($translation)),
                'Перевод Успешно создано',
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
        $trans = $this->trans->getRepo();

        try {
            $trans->getModel()->validate($request->all(), __FUNCTION__);
            $trans->update($request->get('id'), $request->all());

            return $this->successResponse([], 'Перевод Успешно обновлено', 'created');
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
            $this->trans->getRepo()->firstAndDelete($id, $request->get('force') ?? false);
            return $this->successResponse([], null, 'removed');
        } catch (ModelNotFoundException $e) {
            return $this->validationErrorResponse($e->getMessage());
        }
    }
}
