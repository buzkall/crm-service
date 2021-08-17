<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendResponse($result, $message = '', $code = 200): JsonResponse
    {
        $response = ['success' => true,
                     'message' => $message,
                     'data'    => $result];

        return response()->json($response, $code);
    }

    public function sendSuccess($message): JsonResponse
    {
        return $this->sendResponse([], $message);
    }

    public function sendCreated($result, $message = null): JsonResponse
    {
        if (is_null($message)) {
            $message = 'Successfully created';
        }
        return $this->sendResponse($result, $message, 201);
    }

    public function sendNoContent(): JsonResponse
    {
        return $this->sendResponse([], 'Successfully deleted', 204);
    }

    public function sendError($error, $errorMessages = [], $code = 404): JsonResponse
    {
        $response = ['success' => false,
                     'message' => $error];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    public function sendUnauthorized($error): JsonResponse
    {
        return $this->sendError($error, [], 401);
    }
}
