<?php
namespace App\Service;

use App\Entity\DataSource;
use App\DataIngestion\Domain\ApiFetcher;
use App\DataIngestion\Domain\FileUploader;

readonly class DataFetcherService
{
    public function __construct(private ApiFetcher $apiFetcher, private FileUploader $fileUploader)
    {
    }

    /**
     * Fetch data from an API using the DataSource's API endpoint.
     *
     * @param DataSource $dataSource
     * @return array
     * @throws \InvalidArgumentException
     */
    public function fetchFromApi(DataSource $dataSource): array
    {
        $endpoint = $dataSource->getApiEndpoint();
        if (empty($endpoint)) {
            throw new \InvalidArgumentException("API endpoint is not defined for this data source.");
        }
        return $this->apiFetcher->fetch($dataSource);
    }

    /**
     * Process an uploaded file using the DataSource's file path.
     *
     * @param DataSource $dataSource
     * @return array
     * @throws \InvalidArgumentException
     */
    public function processUploadedFile(DataSource $dataSource): array
    {
        $filePath = $dataSource->getFilePath();
        if (empty($filePath)) {
            throw new \InvalidArgumentException("File path is not defined for this data source.");
        }
        return $this->fileUploader->processFile($filePath);
    }
}
