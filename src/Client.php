<?php

namespace JanWennrich\BoardGameGeekApi;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use JanWennrich\BoardGameGeekApi\Search\Query;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Client
{
    const API_URL = 'https://boardgamegeek.com/xmlapi2';

    private string $userAgent = 'BGG XML API Client/1.0';
    private ?string $authorization = null;

    private LoggerInterface $logger;
    private GuzzleClient $httpClient;
    private CookieJar $cookieJar;

    public function __construct(?LoggerInterface $logger = null, ?GuzzleClient $httpClient = null)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->cookieJar = new CookieJar();

        $this->httpClient = $httpClient ?? new GuzzleClient([
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
     * @throws Exception
     */
    public function getThing(int $id, bool $stats = false, bool $versions = false): ?Thing
    {
        if (empty($id)) {
            return null;
        }

        $xml = $this->request('thing', [
            'id' => $id,
            'stats' => $stats,
            'versions' => $versions,
        ]);

        return Factory::fromXml($xml->item);
    }

    /**
     * @return Thing[]
     * @throws Exception
     */
    public function getThings(array $ids, bool $stats = false, bool $versions = false): array
    {
        if (empty($ids)) {
            return [];
        }

        $xml = $this->request('thing', [
            'id' => join(',', $ids),
            'stats' => $stats,
            'versions' => $versions,
        ]);

        $items = [];
        foreach ($xml as $item) {
            $items[] = Factory::fromXml($item);
        }

        return $items;
    }

    /**
     * https://boardgamegeek.com/wiki/page/BGG_XML_API2#toc11
     * TODO: Note that you should check the response status code... if it's 202 (vs. 200) then it indicates BGG has queued
     * your request and you need to keep retrying (hopefully w/some delay between tries) until the status is not 202.
     *
     * @throws Exception
     */
    public function getCollection(array $params): Collection
    {
        $xml = $this->request('collection', $params);
        if ($xml->getName() != 'items') {
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

        return !empty($xml['id'])
            ? new User($xml)
            : null;

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
                    'action' => $action
                ]);

                sleep(5);
            }

            $startTime = microtime(true);

            try {
                $httpResponse = $this->httpClient->request('GET', $action, [
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
            $response = $this->httpClient->request('POST', $url, [
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
        } catch (GuzzleException $exception) {
            $this->logger->error('BGG login request failed', [
                'exception' => $exception,
            ]);
            throw new \Exception('Login request failed', 0, $exception);
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
