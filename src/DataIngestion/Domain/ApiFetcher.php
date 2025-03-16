<?php
namespace App\DataIngestion\Domain;

use App\Entity\DataSource;
use App\Enum\ApiResponseContentTypeEnum;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class ApiFetcher
{
    public function __construct(private HttpClientInterface $client)
    {
    }

    public function fetch(DataSource $dataSource): array
    {
        $response = $this->client->request('GET', $dataSource->getApiEndpoint());

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException("API error: HTTP " . $response->getStatusCode());
        }

        return $this->parseResponse($response->getContent(), $dataSource->getApiResponseContentType());
    }

    private function parseResponse(string $content, ?ApiResponseContentTypeEnum $contentType): array
    {
        return match ($contentType) {
            ApiResponseContentTypeEnum::JSON => json_decode($content, true) ?? throw new \RuntimeException("Invalid JSON response"),
            ApiResponseContentTypeEnum::CSV => $this->parseCsv($content),
            ApiResponseContentTypeEnum::XML => $this->parseXml($content),
            default => throw new \RuntimeException("Unsupported content type"),
        };
    }

    private function parseCsv(string $csvContent): array
    {
        $lines = explode("\n", trim($csvContent));
        $headers = str_getcsv(array_shift($lines));
        return array_map(fn($line) => array_combine($headers, str_getcsv($line)), $lines);
    }

    private function parseXml(string $xmlContent): array
    {
        $xml = simplexml_load_string($xmlContent, "SimpleXMLElement", LIBXML_NOCDATA);
        return json_decode(json_encode($xml), true);
    }
}
