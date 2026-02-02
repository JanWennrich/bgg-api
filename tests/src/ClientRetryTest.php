<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi;

namespace JanWennrich\BoardGameGeekApi\Test;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use JanWennrich\BoardGameGeekApi\Client;
use JanWennrich\BoardGameGeekApi\ClientRequestException;
use JanWennrich\BoardGameGeekApi\RetryConfig;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

#[CoversMethod(Client::class, 'request')]
final class ClientRetryTest extends TestCase
{
    public function testAttemptNumberWhenClientErrorBreaksEarly(): void
    {
        $client = $this->makeClient([
            new Response(400, [], 'client error'),
        ], new RetryConfig(maxAttempts: 3, initialExponentialRetryDelayInSeconds: 1));

        try {
            $client->getHotItems();
            $this->fail('Expected ClientRequestException to be thrown.');
        } catch (ClientRequestException $clientRequestException) {
            $this->assertSame(1, $clientRequestException->attemptNumber);
        }
    }

    public function testAttemptNumberWhenRetriesAreExhausted(): void
    {
        $client = $this->makeClient([
            new Response(202, [], 'queued'),
            new Response(202, [], 'queued'),
            new Response(202, [], 'queued'),
        ], new RetryConfig(maxAttempts: 3, initialExponentialRetryDelayInSeconds: 1));

        try {
            $client->getHotItems();
            $this->fail('Expected ClientRequestException to be thrown.');
        } catch (ClientRequestException $clientRequestException) {
            $this->assertSame(3, $clientRequestException->attemptNumber);
        }
    }

    /**
     * @param list<ResponseInterface> $responses
     */
    private function makeClient(array $responses, RetryConfig $retryConfig): Client
    {
        $mockHandler = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mockHandler);
        $client = new GuzzleClient(['handler' => $handlerStack]);

        return new Client(retryConfig: $retryConfig, guzzleClient: $client);
    }
}
