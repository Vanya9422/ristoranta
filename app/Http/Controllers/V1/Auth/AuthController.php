<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Eloquent\Business\TableInterface;
use App\Services\FirebaseService;
use App\Services\UserService;
use App\Traits\Responsable;
use Firebase\Auth\Token\Exception\ExpiredToken;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Class AuthController
 * @package App\Http\Controllers\V1\Auth
 */
class AuthController extends Controller
{
    use Responsable;

    /**
     * @var UserService
     */
    private UserService $user;

    /**
     * @var FirebaseService
     */
    private FirebaseService $firebaseService;

    /**
     * @var Auth
     */
    private Auth $auth;

    /**
     * AuthFirebaseController constructor.
     * @param UserService $user
     * @param FirebaseService $firebaseService
     * @param Auth $auth
     */
    public function __construct(UserService $user, FirebaseService $firebaseService, Auth $auth)
    {
        $this->user = $user;
        $this->firebaseService = $firebaseService;
        $this->auth = $auth;
    }

    /**
     * @param Request $request
     * @param TableInterface $table
     * @return JsonResponse
     */
    public function tableAuth(Request $request, TableInterface $table): JsonResponse
    {
        try {
            $table->getModel()->validate($request->all(), __FUNCTION__);

            $tableId = $request->get('table_id');

            $token = $this->auth::tokenById($tableId);

            $waiter = $table->find($tableId)->waiter()->first();

            return $this->respondWithToken($token, (new UserResource($waiter)));
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->validationErrorResponse('Срок действия токена истек');
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->validationErrorResponse('Токен недействителен');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $model = $this->user->getRepo()->getModel();

            /** TODO Custom Validate Class With Validates Actions **/
            $model->validate($request->all(), __FUNCTION__);

            $model->getConnectionResolver()->transaction(function () use ($request, &$token, &$user) {

                $this->firebaseService->verifyToken($request->bearerToken());

                $credentials = $request->only(['phone', 'password']);

                $phone_number = $this->firebaseService->getPhone();

                throw_if($credentials['phone'] !== $phone_number, 'Номера не совподают');

                $uid = $this->firebaseService->getUid();

                $user = $this->user->createRegisterUser(
                    array_merge($credentials, ['uid' => $uid])
                );

                $token = $this->auth::tokenById($user->id);
            });

            return $this->respondWithToken($token, (new UserResource($user)), 'created');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (ExpiredToken $e) {
            return $this->clientErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function passwordMake(Request $request): JsonResponse
    {
        try {
            $model = $this->user->getRepo()->getModel();

            $model->validate($request->all(), __FUNCTION__);

            $user = $this->user->getRepo()->findByCriteria(['phone' => $request->get('phone')]);

            $user->update(['password' => $request->get('password')]);

            return $this->successResponse([], 'Пароль успешно добавлен');
        } catch (ModelNotFoundException $e) {
            return $this->validationErrorResponse($e->getMessage());
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $this->user->getRepo()->getModel()->validate($request->all(), __FUNCTION__);

            $credentials = $request->only(['phone', 'password']);

            if (!$token = $this->auth::attempt($credentials)) {
                return $this->authorizedErrorResponse();
            }

            return $this->respondWithToken($token, (new UserResource($this->auth::user())));
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->validationErrorResponse('Срок действия токена истек');
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->validationErrorResponse('Токен недействителен');
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function passwordReset(Request $request, User $user): JsonResponse
    {
        $model = $this->user->getRepo()->getModel();

        try {
            $model->validate($request->all(), __FUNCTION__);

            $this->firebaseService->verifyToken($request->bearerToken());

            $model->getConnectionResolver()->transaction(function () use ($request, &$token, &$user) {

                $credentials = $request->only(['phone', 'password']);
                $uid = $this->firebaseService->getUid();

                $user = $this->user->getRepo()->updateOrCreate(
                    ['uid' => $uid],
                    ['password' => $credentials['password']]
                );

                $token = $this->auth::tokenById($user->id);
            });

            return $this->respondWithToken($token, (new UserResource($user)));
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (ExpiredToken $e) {
            return $this->clientErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get the authenticated User
     *
     * @return JsonResponse
     */
    public function authUser(): JsonResponse
    {
        return $this->successResponse((new UserResource($this->auth::user())));
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->auth::logout(true);
        return $this->successResponse('Успешный выход из системы');
    }
}
