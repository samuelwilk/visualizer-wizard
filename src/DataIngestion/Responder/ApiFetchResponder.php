<?php

namespace App\DataIngestion\Responder;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiFetchResponder
{
    public function respondWithData(array $data): JsonResponse
    {
        return new JsonResponse(['status' => 'success', 'data' => $data]);
    }

    public function respondWithError(string $error): JsonResponse
    {
        return new JsonResponse(['status' => 'error', 'message' => $error], 500);
    }
}
