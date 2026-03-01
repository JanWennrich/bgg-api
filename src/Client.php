<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use JanWennrich\BoardGameGeekApi\Family\FamilyItem;
use JanWennrich\BoardGameGeekApi\Query\CollectionQuery;
use JanWennrich\BoardGameGeekApi\Query\FamilyQuery;
use JanWennrich\BoardGameGeekApi\Query\GuildQuery;
use JanWennrich\BoardGameGeekApi\Query\PlaysQuery;
use JanWennrich\BoardGameGeekApi\Query\SearchQuery;
use JanWennrich\BoardGameGeekApi\Query\ThreadQuery;
use JanWennrich\BoardGameGeekApi\Query\ThingQuery;
use JanWennrich\BoardGameGeekApi\Query\UsersQuery;
use JanWennrich\BoardGameGeekApi\Collection\Collection;
use JanWennrich\BoardGameGeekApi\Collection\CollectionMapper;
use JanWennrich\BoardGameGeekApi\Family\FamilyMapper;
use JanWennrich\BoardGameGeekApi\Forum\Forum;
use JanWennrich\BoardGameGeekApi\Forum\ForumMapper;
use JanWennrich\BoardGameGeekApi\ForumList\ForumList;
use JanWennrich\BoardGameGeekApi\ForumList\ForumListMapper;
use JanWennrich\BoardGameGeekApi\ForumList\ForumListType;
use JanWennrich\BoardGameGeekApi\Guild\Guild;
use JanWennrich\BoardGameGeekApi\Guild\GuildMapper;
use JanWennrich\BoardGameGeekApi\Hot\HotItemType;
use JanWennrich\BoardGameGeekApi\Hot\HotMapper;
use JanWennrich\BoardGameGeekApi\Plays\Plays;
use JanWennrich\BoardGameGeekApi\Plays\PlaysMapper;
use JanWennrich\BoardGameGeekApi\Search\Search;
use JanWennrich\BoardGameGeekApi\Search\SearchMapper;
use JanWennrich\BoardGameGeekApi\Thing\Thing;
use JanWennrich\BoardGameGeekApi\Thing\ThingMapper;
use JanWennrich\BoardGameGeekApi\Thread\Thread;
use JanWennrich\BoardGameGeekApi\Thread\ThreadMapper;
use JanWennrich\BoardGameGeekApi\User\User;
use JanWennrich\BoardGameGeekApi\User\UserMapper;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

/**
 * @phpstan-type RequestParams Array<string, bool|int|string>
 * @phpstan-type BggId positive-int
 */
final class Client
{
    /**
     * @var non-empty-string|null
     */
    private ?string $apiToken = null;

    /**
     * @param ClientInterface $psr18Client The HTTP Client must support storing and sending cookies, to use the login functionality
     */
    public function __construct(
        private readonly ClientInterface $psr18Client,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly RetryConfig $retryConfig = new RetryConfig(),
        private string $userAgent = 'jan-wennrich/bgg-api Client/1.0',
        private readonly SleepServiceInterface $sleepService = new SleepService(),
        private readonly LoggerInterface $logger = new NullLogger(),
        private readonly ThingMapper $thingMapper = new ThingMapper(),
        private readonly ForumListMapper $forumListMapper = new ForumListMapper(),
        private readonly ForumMapper $forumMapper = new ForumMapper(),
        private readonly ThreadMapper $threadMapper = new ThreadMapper(),
        private readonly UserMapper $userMapper = new UserMapper(),
        private readonly GuildMapper $guildMapper = new GuildMapper(),
        private readonly FamilyMapper $familyMapper = new FamilyMapper(),
        private readonly CollectionMapper $collectionMapper = new CollectionMapper(),
        private readonly HotMapper $hotMapper = new HotMapper(),
        private readonly SearchMapper $searchMapper = new SearchMapper(),
        private readonly PlaysMapper $playsMapper = new PlaysMapper(),
    ) {}

    /**
     * Build a client using php-http/discovery to locate PSR-18/PSR-17 implementations.
     */
    public static function autocreate(): self
    {
        return new self(
            psr18Client: Psr18ClientDiscovery::find(),
            requestFactory: Psr17FactoryDiscovery::findRequestFactory(),
            streamFactory: Psr17FactoryDiscovery::findStreamFactory(),
        );
    }

    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Set the Authorization header value to be sent with all API requests.
     * Pass null to disable sending the Authorization header.
     *
     * @param non-empty-string|null $apiToken
     */
    public function setApiToken(?string $apiToken): self
    {
        $this->apiToken = $apiToken;
        return $this;
    }

    /**
     * @param BggId $id The id of the thing to retrieve. Cannot be an empty array.
     * @param ?ThingQuery $thingQuery Query to modify and filter the returned result
     *
     * @throws Exception
     *
     * @throws InvalidArgumentException
     */
    public function getThing(int $id, ?ThingQuery $thingQuery = null): ?Thing
    {
        Assert::positiveInteger($id);

        $query = $thingQuery?->toQueryParameters() ?? [];

        $query['id'] = $id;

        $xml = $this->request(BggApiEndpoint::Thing, $query);

        if (!property_exists($xml, 'item') || $xml->item === null) {
            return null;
        }

        return $this->thingMapper->fromXml($xml->item);
    }

    /**
     * @param non-empty-array<BggId> $ids The ids of the things to retrieve. Must contain between 1 and 20 ids.
     * @param ?ThingQuery $thingQuery Query to modify and filter the returned result
     *
     * @return Thing[]
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getThings(array $ids, ?ThingQuery $thingQuery = null): array
    {
        Assert::countBetween($ids, 1, 20);
        Assert::allPositiveInteger($ids);

        $query = $thingQuery?->toQueryParameters() ?? [];

        $query['id'] = implode(',', $ids);

        $xml = $this->request(BggApiEndpoint::Thing, $query);

        $items = [];
        foreach ($xml as $item) {
            $items[] = $this->thingMapper->fromXml($item);
        }

        return $items;
    }

    /**
     * @param BggId $id The id of the entry to retrieve the forum list for.
     * @param ForumListType $forumListType The type of entry in the database.
     *
     * @throws Exception
     */
    public function getForumList(int $id, ForumListType $forumListType): ForumList
    {
        Assert::positiveInteger($id);

        $xml = $this->request(BggApiEndpoint::ForumList, [
            'id' => $id,
            'type' => $forumListType->value,
        ]);

        return $this->forumListMapper->fromXml($xml);
    }

    /**
     * @param BggId $id The id of the forum. This is the id that appears in the address of the page when visiting a forum in the browser.
     * @param positive-int $page The page of the thread list to return; page size is 50. Threads in the thread list are sorted in order of most recent post.
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getForum(int $id, int $page = 1): Forum
    {
        Assert::positiveInteger($id);
        Assert::positiveInteger($page);

        $xml = $this->request(BggApiEndpoint::Forum, [
            'id' => $id,
            'page' => $page,
        ]);

        return $this->forumMapper->fromXml($xml);
    }

    /**
     * @param BggId $id The id of the thread to retrieve.
     * @param ?ThreadQuery $threadQuery Query to filter the returned result
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getThread(int $id, ?ThreadQuery $threadQuery = null): Thread
    {
        Assert::positiveInteger($id);

        $query = $threadQuery?->toQueryParameters() ?? [];
        $query['id'] = $id;

        $xml = $this->request(BggApiEndpoint::Thread, $query);

        return $this->threadMapper->fromXml($xml);
    }

    /**
     * @param non-empty-string $name Specifies the username.
     * @param ?UsersQuery $usersQuery Query to modify and filter the returned result
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getUser(string $name, ?UsersQuery $usersQuery = null): ?User
    {
        Assert::stringNotEmpty($name);

        $query = $usersQuery?->toQueryParameters() ?? [];
        $query['name'] = $name;

        $xml = $this->request(BggApiEndpoint::User, $query);

        if (!isset($xml['id'])) {
            return null;
        }

        return $this->userMapper->fromXml($xml);
    }

    /**
     * @param BggId $id ID of the guild you want to view.
     * @param ?GuildQuery $guildQuery Query to modify and filter the returned result
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getGuild(int $id, ?GuildQuery $guildQuery = null): ?Guild
    {
        Assert::positiveInteger($id);

        $query = $guildQuery?->toQueryParameters() ?? [];
        $query['id'] = $id;

        $xml = $this->request(BggApiEndpoint::Guild, $query);

        if (!isset($xml['id'])) {
            return null;
        }

        return $this->guildMapper->fromXml($xml);
    }


    /**
     * @param BggId $id The id of the family to retrieve.
     * @param ?FamilyQuery $familyQuery Query to filter the returned result
     *
     * @return FamilyItem[]|null
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getFamily(int $id, ?FamilyQuery $familyQuery = null): ?array
    {
        Assert::positiveInteger($id);

        $query = $familyQuery?->toQueryParameters() ?? [];

        $query['id'] = $id;

        $xml = $this->request(BggApiEndpoint::Family, $query);

        if (!property_exists($xml, 'item') || $xml->item === null) {
            return null;
        }

        return $this->familyMapper->fromXml($xml);
    }

    /**
     * @param non-empty-array<BggId> $ids The ids of the families to retrieve. Cannot be an empty array.
     * @param ?FamilyQuery $familyQuery Query to filter the returned result
     *
     * @return FamilyItem[]
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getFamilies(array $ids, ?FamilyQuery $familyQuery = null): array
    {
        Assert::minCount($ids, 1);
        Assert::allPositiveInteger($ids);

        $query = $familyQuery?->toQueryParameters() ?? [];

        $query['id'] = implode(',', $ids);

        $xml = $this->request(BggApiEndpoint::Family, $query);

        return $this->familyMapper->fromXml($xml);
    }



    /**
     * @param non-empty-string $username Username to get the collection for
     * @param ?CollectionQuery $collectionQuery Query to filter the returned result
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getCollection(string $username, ?CollectionQuery $collectionQuery = null): Collection
    {
        Assert::stringNotEmpty($username);

        $query = $collectionQuery?->toQueryParameters() ?? [];
        $query['username'] = $username;

        $xml = $this->request(BggApiEndpoint::Collection, $query);
        if ($xml->getName() !== 'items') {
            $message = (string) ($xml->error->message ?? 'Unknown error');
            throw new Exception($message);
        }

        return $this->collectionMapper->fromXml($xml);
    }

    /**
     * @return \JanWennrich\BoardGameGeekApi\Hot\HotItem[]
     * @throws Exception
     */
    public function getHotItems(HotItemType $hotItemType = HotItemType::BoardGame): array
    {
        $xml = $this->request(BggApiEndpoint::Hot, [
            'type' => $hotItemType->value,
        ]);

        return $this->hotMapper->fromXml($xml);
    }

    /**
     * @param non-empty-string $searchTerm String to search
     * @param ?SearchQuery $searchQuery Optional query parameters
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function search(string $searchTerm, ?SearchQuery $searchQuery = null): Search
    {
        Assert::stringNotEmpty($searchTerm);

        $params = $searchQuery?->toQueryParameters() ?? [];
        $params['query'] = $searchTerm;

        $xml = $this->request(BggApiEndpoint::Search, $params);

        return $this->searchMapper->fromXml($xml);
    }

    /**
     * Get plays logged by a particular user.
     * Data is returned in backwards-chronological form.
     *
     * @param non-empty-string $username Name of the player you want to request play information for.
     * @param ?PlaysQuery $playsQuery Optional query to filter the returned result.
     *
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getPlaysForUser(string $username, ?PlaysQuery $playsQuery = null): Plays
    {
        Assert::stringNotEmpty($username);

        $query = $playsQuery?->toQueryParameters() ?? [];
        $query['username'] = $username;

        $xml = $this->request(BggApiEndpoint::Plays, $query);

        return $this->playsMapper->fromXml($xml);
    }

    /**
     * Get plays logged for an item ID.
     * Data is returned in backwards-chronological form.
     *
     * @param BggId $itemId ID of the item you want to request play information for.
     * @param ItemType $itemType Type of the item you want to request play information for.
     * @param ?PlaysQuery $playsQuery Optional query to filter the returned result.
     *
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getPlaysForItem(int $itemId, ItemType $itemType, ?PlaysQuery $playsQuery = null): Plays
    {
        Assert::positiveInteger($itemId);

        $query = $playsQuery?->toQueryParameters() ?? [];
        $query['id'] = $itemId;
        $query['type'] = $itemType->value;

        $xml = $this->request(BggApiEndpoint::Plays, $query);

        return $this->playsMapper->fromXml($xml);
    }

    /**
     * @param RequestParams $params
     * @throws ClientRequestException
     */
    private function request(BggApiEndpoint $bggApiEndpoint, array $params = []): \SimpleXMLElement
    {
        /** @infection-ignore-all */
        $this->logger->debug('BGG API request', ['action' => $bggApiEndpoint, 'params' => $params]);

        $httpCode = null;
        $previousException = null;
        $lastAttempt = 1;

        for ($attempt = 1; $attempt <= $this->retryConfig->maxAttempts; $attempt++) {
            $lastAttempt = $attempt;

            if ($attempt > 1) {
                $delayInSeconds = $this->calculateRetryDelayInSeconds($attempt);
                /** @infection-ignore-all */
                $this->logger->info('Retrying BGG API request (attempt {attempt})', [
                    'attempt' => $attempt,
                    'action' => $bggApiEndpoint,
                ]);

                $this->sleepService->sleep($delayInSeconds);
            }

            $startTime = microtime(true);

            try {
                $url = "https://boardgamegeek.com/xmlapi2/$bggApiEndpoint->value";

                if ($params !== []) {
                    $url .= '?' . http_build_query($params);
                }

                $request = $this->requestFactory
                    ->createRequest('GET', $url)
                    ->withHeader('User-Agent', $this->userAgent);

                if ($this->apiToken !== null) {
                    $request = $request->withHeader('Authorization', "Bearer $this->apiToken");
                }

                $httpResponse = $this->psr18Client->sendRequest($request);
            } catch (\Throwable $exception) {
                $previousException = $exception;

                /** @infection-ignore-all */
                $this->logger->error('BGG API transport error', [
                    'exception' => $exception,
                    'attempt' => $attempt,
                    'action' => $bggApiEndpoint,
                ]);

                if ($this->retryConfig->retryOnTransportErrors) {
                    continue;
                }

                break;
            }

            $httpCode = $httpResponse->getStatusCode();
            $response = $httpResponse->getBody()->getContents();
            $duration = microtime(true) - $startTime;

            /** @infection-ignore-all */
            $this->logger->debug('BGG API response', [
                'code' => $httpCode,
                'duration' => round($duration, 2),
                'action' => $bggApiEndpoint,
                'attempt' => $attempt,
            ]);

            if ($httpCode === 202) {
                /** @infection-ignore-all */
                $this->logger->info('BGG API queued the request', [
                    'code' => $httpCode,
                    'action' => $bggApiEndpoint,
                    'attempt' => $attempt,
                ]);

                if ($this->retryConfig->retryOnQueuedRequest) {
                    continue;
                }

                break;
            }

            if ($httpCode >= 400 && $httpCode <= 499) {
                /** @infection-ignore-all */
                $this->logger->error('BGG API returned client error', [
                    'code' => $httpCode,
                    'action' => $bggApiEndpoint,
                    'attempt' => $attempt,
                ]);

                // Otherwise, continue to the next attempt
                break;
            }

            if ($httpCode >= 500) {
                /** @infection-ignore-all */
                $this->logger->error('BGG API error response', [
                    'code' => $httpCode,
                    'action' => $bggApiEndpoint,
                    'attempt' => $attempt,
                    'response' => substr($response, 0, 1000),
                ]);

                if ($this->retryConfig->retryOnServerError) {
                    continue;
                }

                break;
            }

            $xml = @simplexml_load_string($response);
            if (!$xml instanceof \SimpleXMLElement) {
                /** @infection-ignore-all */
                $this->logger->error('Failed to parse BGG API response as XML', [
                    'action' => $bggApiEndpoint,
                    'attempt' => $attempt,
                    'response' => substr($response, 0, 1000),
                ]);

                if ($this->retryConfig->retryOnInvalidXml) {
                    continue;
                }

                break;
            }

            // If we got here, we have a valid response
            return $xml;
        }

        throw new ClientRequestException('API call failed', $lastAttempt, $httpCode, $previousException);
    }

    /**
     * @param positive-int $attempt
     * @return non-negative-int
     */
    private function calculateRetryDelayInSeconds(int $attempt): int
    {
        $exponent = $attempt - 2;
        if ($exponent < 0) {
            $exponent = 0;
        }

        return $this->retryConfig->initialExponentialRetryDelayInSeconds * (2 ** $exponent);
    }

    /**
     * Log in via username and password.
     * This grants access to data of the given user without an {@see https://boardgamegeek.com/using_the_xml_api authorization token}.
     * In order for this to work, the {@see Psr18Client} used by this class must support sending and receiving cookies.
     *
     * @throws \Exception
     */
    public function login(string $username, string $password): void
    {
        $url = 'https://boardgamegeek.com/login/api/v1';

        /** @infection-ignore-all */
        $this->logger->info('Logging in to BGG');

        try {
            $body = json_encode([
                'credentials' => [
                    'username' => $username,
                    'password' => $password,
                ],
            ], JSON_THROW_ON_ERROR);

            $request = $this->requestFactory
                ->createRequest('POST', $url)
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('User-Agent', $this->userAgent)
                ->withBody($this->streamFactory->createStream($body));

            $response = $this->psr18Client->sendRequest($request);
        } catch (\Throwable $throwable) {
            /** @infection-ignore-all */
            $this->logger->error('BGG login request failed', [
                'exception' => $throwable,
            ]);
            throw new \Exception('Login request failed', 0, $throwable);
        }

        $status = $response->getStatusCode();
        $body = $response->getBody()->getContents();

        /** @infection-ignore-all */
        $this->logger->debug('BGG login response', [
            'status' => $status,
            'body' => substr($body, 0, 500),
        ]);

        if ($status >= 300) {
            throw new \Exception('BGG login failed with status ' . $status);
        }
    }
}
