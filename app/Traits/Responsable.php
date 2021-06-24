<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


trait Responsable
{
    /**
     * Response Status Codes From Api
     *
     * @var array[] $statusCodes
     */
    protected static array $statusCodes = [
        'done' => 200,
        'created' => 201,
        'accepted' => 202,
        'removed' => 204,
        'not_modified' => 204,
        'not_valid' => 400,
        'not_found' => 404,
        'forbidden' => 403,
        'unauthorized' => 401,
        'permissions' => 403,
        'unprocessable' => 422,
    ];

    /**
     * @param array $data
     * @param string $message
     * @param int|string $code
     * @return JsonResponse
     */
    protected function successResponse($data = [], $message = '', $code = 200): JsonResponse
    {
        $response = [
            'code' => is_string($code) ? self::$statusCodes[$code] : $code,
            'status' => 'success',
        ];

        if ($message)
            $response = array_merge($response, ['message' => $message]);

        if (!empty($data)) {
            if (isset($data['data'])) {
                $response = array_merge($response, $data);
            } else {
                $response = array_merge($response, ['data' => $data]);
            }
        }

        return response()->json($response, is_string($code) ? self::$statusCodes[$code] : $code);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function validationErrorResponse($message = ''): JsonResponse
    {
        return response()->json([
            'code' => self::$statusCodes['not_valid'],
            'status' => 'error',
            'messages' => $message
        ], self::$statusCodes['not_valid']);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function notFoundResponse($message = null): JsonResponse
    {
        return response()->json([
            'code' => self::$statusCodes['not_found'],
            'status' => 'error',
            'message' => $message ?: 'Ресурс не найден'
        ], self::$statusCodes['not_found']);
    }

    /**
     * @param $message
     * @param null|int $code
     * @return JsonResponse
     */
    protected function clientErrorResponse($message = null, $code = null): JsonResponse
    {
        return response()->json([
            'code' => $code ?: self::$statusCodes['unprocessable'],
            'status' => 'error',
            'message' => $message ?: 'Необработанная сущность'
        ], $code ?: self::$statusCodes['unprocessable']);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function authorizedErrorResponse(string $message = '401 Authorization required'): JsonResponse
    {
        return response()->json([
            'code' => self::$statusCodes['unauthorized'],
            'status' => 'error',
            'message' => $message
        ], self::$statusCodes['unauthorized']);
    }

    /**
     * @return JsonResponse
     */
    protected function permissionErrorResponse(): JsonResponse
    {
        return response()->json([
            'code' => self::$statusCodes['permissions'],
            'status' => 'info',
            'message' => "You don't have permission for this action"
        ], self::$statusCodes['permissions']);
    }

    /**
     * @param $token
     * @param User|null $user
     * @param int $code
     * @return JsonResponse
     */
    protected function respondWithToken($token, $user = null, $code = 200): JsonResponse
    {
        $response = [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ];

        if ($user)
            $response = array_merge($response, ['user' => $user]);

        return response()->json($response, is_string($code) ? self::$statusCodes[$code] : $code);
    }
}
