<?php

namespace JanWennrich\BoardGameGeekApi;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use JanWennrich\BoardGameGeekApi\Query\CollectionQuery;
use JanWennrich\BoardGameGeekApi\Query\FamilyQuery;
use JanWennrich\BoardGameGeekApi\Query\GuildQuery;
use JanWennrich\BoardGameGeekApi\Query\PlaysQuery;
use JanWennrich\BoardGameGeekApi\Query\SearchQuery;
use JanWennrich\BoardGameGeekApi\Query\ThreadQuery;
use JanWennrich\BoardGameGeekApi\Query\ThingQuery;
use JanWennrich\BoardGameGeekApi\Query\UsersQuery;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

/**
 * @phpstan-type RequestParams Array<string, bool|int|string>
 * @phpstan-type BggId positive-int
 */
class Client
{
    public const API_URL = 'https://boardgamegeek.com/xmlapi2';

    private string $userAgent = 'BGG XML API Client/1.0';

    private ?string $authorization = null;

    private readonly GuzzleClient $guzzleClient;

    private readonly CookieJar $cookieJar;

    public function __construct(private readonly LoggerInterface $logger = new NullLogger(), ?GuzzleClient $guzzleClient = null)
    {
        $this->cookieJar = new CookieJar();

        $this->guzzleClient = $guzzleClient ?? new GuzzleClient([
            'base_uri' => self::API_URL . '/',
            'timeout' => 30,
            'cookies' => $this->cookieJar,
            'headers' => [
                'Accept-Encoding' => 'gzip',
            ],
        ]);
    }

    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Set the Authorization header value to be sent with all API requests.
     * Pass null to disable sending the Authorization header.
     */
    public function setAuthorization(?string $authorization): self
    {
        $this->authorization = $authorization;
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

        $query = $this->buildThingQueryArray($thingQuery);

        $query['id'] = $id;

        $xml = $this->request('thing', $query);

        return Factory::fromXml($xml->item);
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

        $query = $this->buildThingQueryArray($thingQuery);

        $query['id'] = implode(',', $ids);

        $xml = $this->request('thing', $query);

        $items = [];
        foreach ($xml as $item) {
            $thing = Factory::fromXml($item);

            if (!$thing instanceof Thing) {
                continue;
            }

            $items[] = $thing;
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
        $xml = $this->request('forumlist', [
            'id' => $id,
            'type' => $forumListType->value,
        ]);

        return new ForumList($xml);
    }

    /**
     * @param BggId $id The id of the forum. This is the id that appears in the address of the page when visiting a forum in the browser.
     * @param positive-int $page The page of the thread list to return; page size is 50. Threads in the thread list are sorted in order of most recent post.
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getForum(int $id, int $page = 1): ForumThreadList
    {
        Assert::positiveInteger($id);
        Assert::positiveInteger($page);

        $xml = $this->request('forum', [
            'id' => $id,
            'page' => $page,
        ]);

        return new ForumThreadList($xml);
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

        $query = $this->buildThreadQueryArray($threadQuery);
        $query['id'] = $id;

        $xml = $this->request('thread', $query);

        return new Thread($xml);
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

        $query = $this->buildUsersQueryArray($usersQuery);
        $query['name'] = $name;

        $xml = $this->request('users', $query);

        return empty($xml['id']) ? null : new User($xml);
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

        $query = $this->buildGuildQueryArray($guildQuery);
        $query['id'] = $id;

        $xml = $this->request('guild', $query);

        return empty($xml['id']) ? null : new Guild($xml);
    }

    /**
     * @return ($thingQuery is null ? array{} : array{
     *     types: literal-string,
     *     versions: int<0,1>,
     *     videos: int<0,1>,
     *     stats: int<0,1>,
     *     marketplace: int<0,1>,
     *     comments: int<0,1>,
     *     ratingcomments: int<0,1>,
     *     page: positive-int,
     *     pagesize: int<10, 100>
     * })
     */
    private function buildThingQueryArray(?ThingQuery $thingQuery = null): array
    {
        if (!$thingQuery instanceof ThingQuery) {
            return [];
        }

        return [
            'types' => implode(
                ',',
                array_map(static fn(ThingType $thingType) => $thingType->value, $thingQuery->withTypes),
            ),
            'versions' => (int) $thingQuery->withVersions,
            'videos' => (int) $thingQuery->withVideos,
            'stats' => (int) $thingQuery->withStats,
            'marketplace' => (int) $thingQuery->withMarketplaceData,
            'comments' => (int) $thingQuery->withComments,
            'ratingcomments' => (int) $thingQuery->withRatingComments,
            'page' => $thingQuery->page,
            'pagesize' => $thingQuery->pageSize,
        ];
    }

    /**
     * @return ($threadQuery is null ? array{} : array{
     *     minarticleid?: positive-int,
     *     minarticledate?: non-empty-string,
     *     count?: int<1,1000>,
     * })
     */
    private function buildThreadQueryArray(?ThreadQuery $threadQuery = null): array
    {
        if (!$threadQuery instanceof ThreadQuery) {
            return [];
        }

        return array_filter([
            'minarticleid' => $threadQuery->minArticleId,
            'minarticledate' => $threadQuery->minArticleDate?->format('Y-m-d H:i:s'),
            'count' => $threadQuery->count,
        ], static fn(mixed $value): bool => $value !== null);
    }

    /**
     * @return ($usersQuery is null ? array{} : array{
     *     buddies: int<0,1>,
     *     guilds: int<0,1>,
     *     hot: int<0,1>,
     *     top: int<0,1>,
     *     domain: value-of<UserDomain>,
     *     page: positive-int
     * })
     */
    private function buildUsersQueryArray(?UsersQuery $usersQuery = null): array
    {
        if (!$usersQuery instanceof UsersQuery) {
            return [];
        }

        return [
            'buddies' => (int) $usersQuery->withBuddies,
            'guilds' => (int) $usersQuery->withGuilds,
            'hot' => (int) $usersQuery->withHot,
            'top' => (int) $usersQuery->withTop,
            'domain' => $usersQuery->domain->value,
            'page' => $usersQuery->page,
        ];
    }

    /**
     * @return ($guildQuery is null ? array{} : array{
     *     members: int<0,1>,
     *     sort: value-of<GuildMemberSort>,
     *     page: positive-int
     * })
     */
    private function buildGuildQueryArray(?GuildQuery $guildQuery = null): array
    {
        if (!$guildQuery instanceof GuildQuery) {
            return [];
        }

        return [
            'members' => (int) $guildQuery->withMembers,
            'sort' => $guildQuery->sort->value,
            'page' => $guildQuery->page,
        ];
    }

    /**
     * @return ($searchQuery is null ? array{} : array{
     *     type?: non-empty-string,
     *     exact: int<0,1>
     * })
     */
    private function buildSearchQueryArray(?SearchQuery $searchQuery = null): array
    {
        if (!$searchQuery instanceof SearchQuery) {
            return [];
        }

        $onlyTypesString = null;
        if ($searchQuery->onlyTypes !== []) {
            $onlyTypesString = implode(
                ',',
                array_map(
                    static fn(SearchType $searchType): string => $searchType->value,
                    $searchQuery->onlyTypes,
                ),
            );
        }

        return array_filter([
            'type' => $onlyTypesString,
            'exact' => (int) $searchQuery->onlyExact,
        ], static fn(mixed $value): bool => $value !== null);
    }

    /**
     * @return ($collectionQuery is null ? array{} : array{
     *     version: int<0,1>,
     *     subtype: value-of<ThingType>,
     *     excludesubtype?: value-of<ThingType>,
     *     id?: non-empty-string,
     *     brief: int<0,1>,
     *     stats: int<0,1>,
     *     own?: int<0,1>,
     *     rated?: int<0,1>,
     *     played?: int<0,1>,
     *     comment?: int<0,1>,
     *     trade?: int<0,1>,
     *     want?: int<0,1>,
     *     wishlist?: int<0,1>,
     *     wishlistpriority?: int<1,5>,
     *     preordered?: int<0,1>,
     *     wanttoplay?: int<0,1>,
     *     wanttobuy?: int<0,1>,
     *     prevowned?: int<0,1>,
     *     hasparts?: int<0,1>,
     *     wantparts?: int<0,1>,
     *     minrating?: int<1,10>,
     *     rating?: int<1,10>,
     *     minbggrating?: int<1,10>,
     *     bggrating?: int<1,10>,
     *     minplays?: int<0,max>,
     *     maxplays?: int<0,max>,
     *     showprivate: int<0,1>,
     *     collid?: positive-int,
     *     modifiedsince?: non-empty-string
     * })
     */
    private function buildCollectionQueryArray(?CollectionQuery $collectionQuery = null): array
    {
        if (!$collectionQuery instanceof CollectionQuery) {
            return [];
        }

        $ids = null;
        if (is_array($collectionQuery->ids)) {
            $ids = implode(',', $collectionQuery->ids);
        }

        return array_filter([
            'version' => (int) $collectionQuery->withVersions,
            'subtype' => $collectionQuery->onlyThingsWithType->value,
            'excludesubtype' => $collectionQuery->excludeThingsWithType?->value,
            'id' => $ids,
            'brief' => (int) $collectionQuery->onlyBrief,
            'stats' => (int) $collectionQuery->withStats,
            'own' => $collectionQuery->isOwned === null ? null : (int) $collectionQuery->isOwned,
            'rated' => $collectionQuery->isRated === null ? null : (int) $collectionQuery->isRated,
            'played' => $collectionQuery->isPlayed === null ? null : (int) $collectionQuery->isPlayed,
            'comment' => $collectionQuery->isCommented === null ? null : (int) $collectionQuery->isCommented,
            'trade' => $collectionQuery->isForTrade === null ? null : (int) $collectionQuery->isForTrade,
            'want' => $collectionQuery->isWanted === null ? null : (int) $collectionQuery->isWanted,
            'wishlist' => $collectionQuery->isWishlisted === null ? null : (int) $collectionQuery->isWishlisted,
            'wishlistpriority' => $collectionQuery->wishlistPriority,
            'preordered' => $collectionQuery->isPreOrdered === null ? null : (int) $collectionQuery->isPreOrdered,
            'wanttoplay' => $collectionQuery->wantToPlay === null ? null : (int) $collectionQuery->wantToPlay,
            'wanttobuy' => $collectionQuery->wantToBuy === null ? null : (int) $collectionQuery->wantToBuy,
            'prevowned' => $collectionQuery->isPreviouslyOwned === null ? null : (int) $collectionQuery->isPreviouslyOwned,
            'hasparts' => $collectionQuery->hasParts === null ? null : (int) $collectionQuery->hasParts,
            'wantparts' => $collectionQuery->wantParts === null ? null : (int) $collectionQuery->wantParts,
            'minrating' => $collectionQuery->minPersonalRating,
            'rating' => $collectionQuery->maxPersonalRating,
            'minbggrating' => $collectionQuery->minBggRating,
            'bggrating' => $collectionQuery->maxBggRating,
            'minplays' => $collectionQuery->minPlays,
            'maxplays' => $collectionQuery->maxPlays,
            'showprivate' => (int) $collectionQuery->showPrivate,
            'collid' => $collectionQuery->collectionId,
            'modifiedsince' => $collectionQuery->modifiedSince?->format('Y-m-d H:i:s'),
        ], static fn(mixed $value): bool => $value !== null);
    }

    /**
     * @return ($playsQuery is null ? array{} : array{
     *     mindate?: non-empty-string,
     *     maxdate?: non-empty-string,
     *     subtype: value-of<PlayType>,
     *     page: positive-int
     * })
     */
    private function buildPlaysQueryArray(?PlaysQuery $playsQuery = null): array
    {
        if (!$playsQuery instanceof PlaysQuery) {
            return [];
        }

        return array_filter([
            'mindate' => $playsQuery->minDate?->format('Y-m-d'),
            'maxdate' => $playsQuery->maxDate?->format('Y-m-d'),
            'subtype' => $playsQuery->playType->value,
            'page' => $playsQuery->page,
        ], static fn(mixed $value): bool => $value !== null);
    }


    /**
     * @param BggId $id The id of the family to retrieve.
     * @param ?FamilyQuery $familyQuery Query to filter the returned result
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getFamily(int $id, ?FamilyQuery $familyQuery = null): ?Family
    {
        Assert::positiveInteger($id);

        $query = $this->buildFamilyQueryArray($familyQuery);

        $query['id'] = $id;

        $xml = $this->request('family', $query);

        if (!isset($xml->item)) {
            return null;
        }

        return new Family($xml->item);
    }

    /**
     * @param non-empty-array<BggId> $ids The ids of the families to retrieve. Cannot be an empty array.
     * @param ?FamilyQuery $familyQuery Query to filter the returned result
     *
     * @return Family[]
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getFamilies(array $ids, ?FamilyQuery $familyQuery = null): array
    {
        Assert::minCount($ids, 1);
        Assert::allPositiveInteger($ids);

        $query = $this->buildFamilyQueryArray($familyQuery);

        $query['id'] = implode(',', $ids);

        $xml = $this->request('family', $query);

        $items = [];
        foreach ($xml as $item) {
            $items[] = new Family($item);
        }

        return $items;
    }

    /**
     * @return ($familyQuery is null ? array{} : array{
     *     type: literal-string
     * })
     */
    private function buildFamilyQueryArray(?FamilyQuery $familyQuery = null): array
    {
        if (!$familyQuery instanceof FamilyQuery) {
            return [];
        }

        return [
            'type' => implode(
                ',',
                array_map(static fn(FamilyType $familyType) => $familyType->value, $familyQuery->withTypes),
            ),
        ];
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

        $query = $this->buildCollectionQueryArray($collectionQuery);
        $query['username'] = $username;

        $xml = $this->request('collection', $query);
        if ($xml->getName() !== 'items') {
            throw new Exception($xml->error->message);
        }

        return new Collection($xml);
    }

    /**
     * @return HotItem[]
     * @throws Exception
     */
    public function getHotItems(HotItemType $hotItemType = HotItemType::BoardGame): array
    {
        $xml = $this->request('hot', [
            'type' => $hotItemType->value,
        ]);

        $items = [];
        foreach ($xml as $item) {
            $items[] = new HotItem($item);
        }

        return $items;
    }

    /**
     * @param non-empty-string $searchTerm String to search
     * @param ?SearchQuery $searchQuery Optional query parameters
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function search(string $searchTerm, ?SearchQuery $searchQuery = null): Search\Query
    {
        Assert::stringNotEmpty($searchTerm);

        $params = $this->buildSearchQueryArray($searchQuery);
        $params['query'] = $searchTerm;

        $xml = $this->request('search', $params);

        return new Search\Query($xml);
    }

    /**
     * Get plays logged by a particular user.
     * Data is returned in backwards-chronological form.
     *
     * @param non-empty-string $username Name of the player you want to request play information for.
     * @param ?PlaysQuery $playsQuery Optional query to filter the returned result.
     *
     * @return Play[]
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getPlaysForUser(string $username, ?PlaysQuery $playsQuery = null): array
    {
        Assert::stringNotEmpty($username);

        $query = $this->buildPlaysQueryArray($playsQuery);
        $query['username'] = $username;

        $xml = $this->request('plays', $query);

        $items = [];
        foreach ($xml as $item) {
            $items[] = new Play($item);
        }

        return $items;
    }

    /**
     * Get plays logged for an item ID.
     * Data is returned in backwards-chronological form.
     *
     * @param BggId $itemId ID of the item you want to request play information for.
     * @param ItemType $itemType Type of the item you want to request play information for.
     * @param ?PlaysQuery $playsQuery Optional query to filter the returned result.
     *
     * @return Play[]
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getPlaysForItem(int $itemId, ItemType $itemType, ?PlaysQuery $playsQuery = null): array
    {
        Assert::positiveInteger($itemId);

        $query = $this->buildPlaysQueryArray($playsQuery);
        $query['id'] = $itemId;
        $query['type'] = $itemType->value;

        $xml = $this->request('plays', $query);

        $items = [];
        foreach ($xml as $item) {
            $items[] = new Play($item);
        }

        return $items;
    }

    /**
     * @param RequestParams $params
     * @throws ClientRequestException
     */
    protected function request(string $action, array $params = []): \SimpleXMLElement
    {
        $this->logger->debug('BGG API request', ['action' => $action, 'params' => $params]);

        $httpCode = null;
        $previousException = null;
        $maxRetries = 3;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            if ($attempt > 2) {
                $this->logger->info('Retrying BGG API request (attempt {attempt})', [
                    'attempt' => $attempt,
                    'action' => $action,
                ]);

                sleep(5);
            }

            $startTime = microtime(true);

            try {
                $httpResponse = $this->guzzleClient->request('GET', $action, [
                    'query' => $params,
                    'headers' => array_filter([
                        'User-Agent' => $this->userAgent,
                        'Authorization' => $this->authorization,
                    ]),
                    'http_errors' => false, // we handle status codes ourselves
                ]);
            } catch (GuzzleException $exception) {
                $previousException = $exception;

                $this->logger->error('BGG API transport error', [
                    'exception' => $exception,
                    'attempt' => $attempt,
                    'action' => $action,
                ]);

                continue;
            }

            $httpCode = $httpResponse->getStatusCode();
            $response = $httpResponse->getBody()->getContents();
            $duration = microtime(true) - $startTime;

            $this->logger->debug('BGG API response', [
                'code' => $httpCode,
                'duration' => round($duration, 2),
                'action' => $action,
                'attempt' => $attempt,
            ]);

            if ($httpCode === 202) {
                $this->logger->info('BGG API returned 202, retrying', [
                    'code' => $httpCode,
                    'action' => $action,
                    'attempt' => $attempt,
                ]);

                continue;
            }

            if ($httpCode >= 400 && $httpCode <= 499) {
                $this->logger->error('BGG API returned client error', [
                    'code' => $httpCode,
                    'action' => $action,
                    'attempt' => $attempt,
                ]);

                // Otherwise, continue to the next attempt
                break;
            }

            if ($httpCode >= 500) {
                $this->logger->error('BGG API error response', [
                    'code' => $httpCode,
                    'action' => $action,
                    'attempt' => $attempt,
                    'response' => substr($response, 0, 1000),
                ]);

                // Otherwise, continue to the next attempt
                continue;
            }

            $xml = simplexml_load_string($response);
            if (!$xml instanceof \SimpleXMLElement) {
                $this->logger->error('Failed to parse BGG API response as XML', [
                    'action' => $action,
                    'attempt' => $attempt,
                    'response' => substr($response, 0, 1000),
                ]);

                // Otherwise, continue to the next attempt
                continue;
            }

            // If we got here, we have a valid response
            return $xml;
        }

        throw new ClientRequestException('API call failed', $attempt, $httpCode, $previousException);
    }

    /**
     * Log in via username and password.
     * This grants access to data of the given user without an {@see https://boardgamegeek.com/using_the_xml_api authorization token}.
     *
     * @throws \Exception
     */
    public function login(string $username, string $password): void
    {
        $url = 'https://boardgamegeek.com/login/api/v1';

        $this->logger->info('Logging in to BGG');

        try {
            $response = $this->guzzleClient->request('POST', $url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => $this->userAgent,
                ],
                'json' => [
                    'credentials' => [
                        'username' => $username,
                        'password' => $password,
                    ],
                ],
                'http_errors' => false,
            ]);
        } catch (GuzzleException $guzzleException) {
            $this->logger->error('BGG login request failed', [
                'exception' => $guzzleException,
            ]);
            throw new \Exception('Login request failed', 0, $guzzleException);
        }

        $status = $response->getStatusCode();
        $body = $response->getBody()->getContents();

        $this->logger->debug('BGG login response', [
            'status' => $status,
            'body' => substr($body, 0, 500),
        ]);

        if ($status >= 300) {
            throw new \Exception('BGG login failed with status ' . $status);
        }
    }
}
