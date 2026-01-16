<?php

namespace JanWennrich\BoardGameGeekApi;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use JanWennrich\BoardGameGeekApi\Query\FamilyQuery;
use JanWennrich\BoardGameGeekApi\Query\ThingQuery;
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

    private LoggerInterface $logger;

    private GuzzleClient $guzzleClient;

    private CookieJar $cookieJar;

    public function __construct(?LoggerInterface $logger = null, ?GuzzleClient $guzzleClient = null)
    {
        $this->logger = $logger ?? new NullLogger();
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

        var_dump($query);

        $query['id'] = $id;

        $xml = $this->request('thing', $query);

        return Factory::fromXml($xml->item);
    }

    /**
     * @param non-empty-array<BggId> $ids The ids of the things to retrieve. Cannot be an empty array.
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
     * https://boardgamegeek.com/wiki/page/BGG_XML_API2#toc11
     * TODO: Note that you should check the response status code... if it's 202 (vs. 200) then it indicates BGG has queued
     * your request and you need to keep retrying (hopefully w/some delay between tries) until the status is not 202.
     *
     * @param RequestParams $params
     * @throws Exception
     */
    public function getCollection(array $params): Collection
    {
        $xml = $this->request('collection', $params);
        if ($xml->getName() !== 'items') {
            throw new Exception($xml->error->message);
        }

        return new Collection($xml);
    }

    /**
     * @return HotItem[]
     * @throws Exception
     */
    public function getHotItems(string $type = Type::BOARDGAME): array
    {
        $xml = $this->request('hot', [
            'type' => $type,
        ]);

        $items = [];
        foreach ($xml as $item) {
            $items[] = new HotItem($item);
        }

        return $items;
    }

    public function getUser(string $name): ?User
    {
        try {

            $xml = $this->request('user', [
                'name' => $name,
            ]);

            return empty($xml['id'])
                ? null
                : new User($xml);

        } catch (\Exception) {
            return null;
        }
    }

    /**
     * @throws Exception
     */
    public function search(string $query, bool $exact = false, string $type = Type::BOARDGAME): Search\Query
    {
        $xml = $this->request('search', array_filter([
            'query' => $query,
            'type' => $type,
            'exact' => (int) $exact,
        ]));

        return new Search\Query($xml);
    }

    /**
     * @param RequestParams $params
     * @return Play[]
     * @throws Exception
     */
    public function getPlays(array $params): array
    {
        $xml = $this->request('plays', $params);

        $items = [];
        foreach ($xml as $item) {
            $items[] = new Play($item);
        }

        return $items;
    }

    /**
     * @param RequestParams $params
     * @throws Exception
     * @throws \Exception
     */
    protected function request(string $action, array $params = []): \SimpleXMLElement
    {
        $params = array_filter($params);

        $this->logger->debug('BGG API request', ['action' => $action, 'params' => $params]);

        $maxRetries = 3;

        for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
            if ($attempt > 0) {
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

        // This should never be reached due to the exception in the last attempt
        throw new Exception('API call failed');
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
