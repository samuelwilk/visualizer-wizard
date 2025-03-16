<?php
namespace App\DataProcessing\Action;

use App\DataProcessing\Domain\DataProcessor;
use App\DataProcessing\Domain\CacheManager;
use App\DataProcessing\Responder\DataProcessingResponder;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

readonly class DataProcessingAction
{
    public function __construct(
        private DataProcessor $processor,
        private CacheManager $cacheManager,
        private DataProcessingResponder $responder
    ) {
    }

    #[Route('/api/process-data', name: 'process_data', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        // Retrieve data from request body (assumes JSON payload)
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->responder->respondWithError('Invalid data provided.');
        }

        try {
            // Process the data using the domain logic
            $processedData = $this->processor->process($data);
            // Cache the processed data
            $this->cacheManager->cacheData('processed_data', $processedData);
        } catch (\Exception|InvalidArgumentException $e) {
            return $this->responder->respondWithError($e->getMessage());
        }

        return $this->responder->respondWithData($processedData);
    }
}
