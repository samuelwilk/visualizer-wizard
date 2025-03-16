<?php
namespace App\DataProcessing\Domain;

class DataProcessor
{
    /**
     * Pre-process the provided data.
     *
     * @param array $data
     * @return array Processed data
     */
    public function process(array $data): array
    {
        // Example transformation: adjust as needed for your use case
        return array_map(function ($item) {
            // Clean, normalize, or aggregate the data as required
            return $item;
        }, $data);
    }
}
