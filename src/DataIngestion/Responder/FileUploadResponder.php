<?php
namespace App\DataIngestion\Responder;

use Symfony\Component\HttpFoundation\JsonResponse;

class FileUploadResponder
{
    /**
     * Returns a successful JSON response with data.
     *
     * @param array $data
     * @return JsonResponse
     */
    public function respondWithData(array $data): JsonResponse
    {
        return new JsonResponse(['status' => 'success', 'data' => $data]);
    }

    /**
     * Returns an error JSON response.
     *
     * @param string $error
     * @return JsonResponse
     */
    public function respondWithError(string $error): JsonResponse
    {
        return new JsonResponse(['status' => 'error', 'message' => $error], 500);
    }
}
