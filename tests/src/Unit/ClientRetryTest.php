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
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

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
            ->withParameterSetsInOrder(1, 2)
            ->seal();

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
        $sleepService->expects($this->never())
            ->method('sleep')
            ->seal();

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
        } catch (ClientRequestException) {
        }
    }

    public function testRequestResponseDebugLogging(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->method('error');
        $logger->expects($this->exactly(2))
            ->method('debug')
            ->withParameterSetsInOrder(
                'BGG API request',
                'BGG API response',
            )->seal();

        $client = $this->makeClient(
            [
                new Response(500, [], 'foo'),
            ],
            new RetryConfig(retryOnServerError: false),
            logger: $logger,
        );

        try {
            $client->getHotItems();
        } catch (ClientRequestException) {
        }
    }

    public function testLoggingOnQueuedRequest(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->method('debug');
        $logger->expects($this->exactly(3))->method('info')->withParameterSetsInOrder(
            'BGG API queued the request',
            'Retrying BGG API request (attempt {attempt})',
            'BGG API queued the request'
        )->seal();

        $client = $this->makeClient(
            [
                new Response(202, [], 'queued'),
                new Response(202, [], 'queued'),
            ],
            new RetryConfig(maxAttempts: 2, retryOnQueuedRequest: true),
            logger: $logger,
        );

        try {
            $client->getHotItems();
        } catch (ClientRequestException) {
        }
    }

    #[TestWith([400])]
    #[TestWith([499])]
    public function testLoggingOnClientError(int $returnCode): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->method('debug');
        $logger->expects($this->once())->method('error')->withParameterSetsInOrder(
            'BGG API returned client error'
        )->seal();

        $client = $this->makeClient(
            [
                new Response($returnCode, [], 'foo'),
            ],
            new RetryConfig(),
            logger: $logger,
        );

        try {
            $client->getHotItems();
        } catch (ClientRequestException) {
        }
    }

    #[TestWith([500])]
    #[TestWith([599])]
    public function testLoggingOnServerError(int $returnCode): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->method('debug');
        $logger->expects($this->once())->method('error')->withParameterSetsInOrder(
            'BGG API error response'
        )->seal();

        $client = $this->makeClient(
            [
                new Response($returnCode, [], 'foo'),
            ],
            new RetryConfig(retryOnServerError: false),
            logger: $logger,
        );

        try {
            $client->getHotItems();
        } catch (ClientRequestException) {
        }
    }

    public function testLoggingOnInvalidXml(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->method('debug');
        $logger->expects($this->once())->method('error')->withParameterSetsInOrder(
            'Failed to parse BGG API response as XML'
        )->seal();

        $client = $this->makeClient(
            [
                new Response(200, [], 'foo'),
            ],
            new RetryConfig(retryOnInvalidXml: false),
            logger: $logger,
        );

        try {
            $client->getHotItems();
        } catch (ClientRequestException) {
        }
    }

    /**
     * @param list<ResponseInterface> $responses
     */
    private function makeClient(
        array $responses,
        RetryConfig $retryConfig,
        ?SleepServiceInterface $sleepService = null,
        ?LoggerInterface $logger = null,
    ): Client {
        $mockHandler = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mockHandler);
        $client = new GuzzleClient(['handler' => $handlerStack]);
        $httpFactory = new HttpFactory();
        $sleepService ??= new SleepService();
        $logger ??= self::createStub(LoggerInterface::class);

        return new Client(
            psr18Client: $client,
            requestFactory: $httpFactory,
            streamFactory: $httpFactory,
            retryConfig: $retryConfig,
            sleepService: $sleepService,
            logger: $logger,
        );
    }
}
