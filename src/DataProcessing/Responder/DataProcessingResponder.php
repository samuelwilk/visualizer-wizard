<?php
namespace App\DataProcessing\Responder;

use Symfony\Component\HttpFoundation\JsonResponse;

class DataProcessingResponder
{
    /**
     * Respond with successfully processed data.
     *
     * @param array $data
     * @return JsonResponse
     */
    public function respondWithData(array $data): JsonResponse
    {
        return new JsonResponse([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * Respond with an error message.
     *
     * @param string $error
     * @return JsonResponse
     */
    public function respondWithError(string $error): JsonResponse
    {
        return new JsonResponse([
            'status' => 'error',
            'message' => $error,
        ], 500);
    }
}
