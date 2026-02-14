<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Test\Support;

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final readonly class RecordingClient implements ClientInterface
{
    public function __construct(
        private ClientInterface $client,
        private string $outputDir,
    ) {}

    /**
     * @throws ClientExceptionInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $response = $this->client->sendRequest($request);

        $stream = $response->getBody();
        $contents = $stream->getContents();

        if ($this->looksLikeXml($contents)) {
            $this->writeFixture($request, $contents);
        }

        return $response->withBody(Utils::streamFor($contents));
    }

    private function writeFixture(RequestInterface $request, string $contents): void
    {
        [$endpoint, $filename] = $this->buildFilename($request);
        $path = rtrim($this->outputDir, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . $endpoint
            . DIRECTORY_SEPARATOR . $filename;

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0o777, true);
        }

        file_put_contents($path, $contents);
    }

    /**
     * @return array{0: non-empty-string, 1: non-empty-string}
     */
    private function buildFilename(RequestInterface $request): array
    {
        $url = (string) $request->getUri();
        $parts = parse_url($url);
        $path = $parts['path'] ?? '';
        $endpoint = basename($path);
        if ($endpoint === '') {
            $endpoint = 'response';
        }

        $query = $parts['query'] ?? '';
        if ($query === '') {
            return [$endpoint, $endpoint . '.xml'];
        }

        parse_str($query, $params);
        ksort($params);

        $chunks = [];
        foreach ($params as $key => $value) {
            $stringValue = is_array($value) ? implode(',', $value) : $value;

            $chunks[] = $key . '=' . $stringValue;
        }

        $suffix = implode('+', $chunks);
        $suffix = preg_replace('/[^A-Za-z0-9=_\-+]+/', '_', $suffix) ?? $suffix;

        return [$endpoint, $endpoint . '-' . $suffix . '.xml'];
    }

    private function looksLikeXml(string $contents): bool
    {
        $trimmed = ltrim($contents);
        return str_starts_with($trimmed, '<');
    }
}
