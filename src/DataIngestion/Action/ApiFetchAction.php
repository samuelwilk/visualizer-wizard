<?php

namespace App\DataIngestion\Action;

use App\DataIngestion\Domain\ApiFetcher;
use App\DataIngestion\Responder\ApiFetchResponder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

readonly class ApiFetchAction
{
    public function __construct(private ApiFetcher $apiFetcher, private ApiFetchResponder $responder)
    {
    }

    #[Route('/api/fetch', name: 'api_fetch', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $endpoint = $request->query->get('endpoint');
        try {
            $data = $this->apiFetcher->fetch($endpoint);
        } catch (\Exception $e) {
            return $this->responder->respondWithError($e->getMessage());
        }
        return $this->responder->respondWithData($data);
    }
}
