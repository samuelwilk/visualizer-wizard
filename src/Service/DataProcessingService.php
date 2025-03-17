<?php

namespace App\Service;

use App\Entity\BuilderDataSource;

readonly class DataProcessingService
{
    public function __construct() {}

    /**
     * Simulates pre-processing of the data source.
     *
     * @param BuilderDataSource $builderDataSource
     * @return array
     */
    public function processData(BuilderDataSource $builderDataSource): array
    {
        // Simulated data processing logic (This will be expanded later)
        return [
            'processedAt' => new \DateTimeImmutable(),
            'summary' => 'Preprocessed ' . count($builderDataSource->getSelectedColumns()) . ' columns.',
            'rowsProcessed' => rand(100, 10000), // Fake row count for now
        ];
    }
}
