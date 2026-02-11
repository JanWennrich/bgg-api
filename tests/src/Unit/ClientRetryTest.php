<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Test;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use JanWennrich\BoardGameGeekApi\Client;
use JanWennrich\BoardGameGeekApi\ClientRequestException;
use JanWennrich\BoardGameGeekApi\SleepService;
use JanWennrich\BoardGameGeekApi\SleepServiceInterface;
use JanWennrich\BoardGameGeekApi\RetryConfig;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

#[CoversMethod(Client::class, 'request')]
final class ClientRetryTest extends TestCase
{
    public function testAttemptNumberWhenClientErrorBreaksEarly(): void
    {
        $client = $this->makeClient(
            [
                new Response(400, [], 'client error'),
            ],
            new RetryConfig(maxAttempts: 3, initialExponentialRetryDelayInSeconds: 1),
            self::createStub(SleepServiceInterface::class)
        );

        try {
            $client->getHotItems();
            self::fail('Expected ClientRequestException to be thrown.');
        } catch (ClientRequestException $clientRequestException) {
            self::assertSame(1, $clientRequestException->attemptNumber);
        }
    }

    public function testAttemptNumberWhenRetriesAreExhausted(): void
    {
        $client = $this->makeClient(
            [
                new Response(202, [], 'queued'),
                new Response(202, [], 'queued'),
                new Response(202, [], 'queued'),
            ],
            new RetryConfig(maxAttempts: 3, initialExponentialRetryDelayInSeconds: 1),
            self::createStub(SleepServiceInterface::class)
        );

        try {
            $client->getHotItems();
            self::fail('Expected ClientRequestException to be thrown.');
        } catch (ClientRequestException $clientRequestException) {
            self::assertSame(3, $clientRequestException->attemptNumber);
        }
    }

    public function testExponentialBackoffUsesExpectedDelays(): void
    {
        $sleepService = $this->createMock(SleepServiceInterface::class);
        $invokedCount = $this->exactly(2);
        $sleepService->expects($invokedCount)
            ->method('sleep')
            ->withParameterSetsInOrder(1, 2);

        $client = $this->makeClient(
            [
                new Response(202, [], 'queued'),
                new Response(202, [], 'queued'),
                new Response(202, [], 'queued'),
            ],
            new RetryConfig(maxAttempts: 3, initialExponentialRetryDelayInSeconds: 1),
            $sleepService,
        );

        try {
            $client->getHotItems();
            self::fail(sprintf("Expected %s to be thrown.", ClientRequestException::class));
        } catch (ClientRequestException) {
            // Expected: retries exhausted.
        }
    }

    public function testQueuedRequestDoesNotRetryWhenDisabled(): void
    {
        $sleepService = $this->createMock(SleepServiceInterface::class);
        $sleepService->expects($this->never())->method('sleep');

        $client = $this->makeClient(
            [
                new Response(202, [], 'queued'),
            ],
            new RetryConfig(
                retryOnQueuedRequest: false,
            ),
            $sleepService,
        );

        try {
            $client->getHotItems();
            self::fail(sprintf("Expected %s to be thrown.", ClientRequestException::class));
        } catch (ClientRequestException $clientRequestException) {
            self::assertSame(1, $clientRequestException->attemptNumber);
        }
    }

    /**
     * @param list<ResponseInterface> $responses
     */
    private function makeClient(
        array $responses,
        RetryConfig $retryConfig,
        ?SleepServiceInterface $sleepService = null,
    ): Client {
        $mockHandler = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mockHandler);
        $client = new GuzzleClient(['handler' => $handlerStack]);
        $httpFactory = new HttpFactory();
        $sleepService ??= new SleepService();

        return new Client(
            psr18Client: $client,
            requestFactory: $httpFactory,
            streamFactory: $httpFactory,
            retryConfig: $retryConfig,
            sleepService: $sleepService,
        );
    }
}
