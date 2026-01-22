<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Test;

use JanWennrich\BoardGameGeekApi\Client;
use JanWennrich\BoardGameGeekApi\Thing;
use JanWennrich\BoardGameGeekApi\User;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;
use JanWennrich\BoardGameGeekApi;
use Webmozart\Assert\InvalidArgumentException;

#[CoversMethod(Client::class, 'getThing')]
#[CoversMethod(Client::class, 'getHotItems')]
#[CoversMethod(Client::class, 'search')]
#[CoversMethod(Client::class, 'getCollection')]
#[CoversMethod(Client::class, 'getPlays')]
#[CoversMethod(Client::class, 'getUser')]
final class ClientTest extends TestCase
{
    /**
     * https://boardgamegeek.com/boardgame/39856/dixit
     */
    public function testGetThingWithUnknownId(): void
    {
        $client = new Client();
        $client->setAuthorization($this->getAuthorizationToken());

        $thing = $client->getThing(5371611111, new BoardGameGeekApi\Query\ThingQuery(withStats: true));

        $this->assertNotInstanceOf(\JanWennrich\BoardGameGeekApi\Thing::class, $thing);
    }

    /**
     * https://boardgamegeek.com/boardgame/39856/dixit
     */
    public function testGetThing(): void
    {
        $client = new Client();
        $client->setAuthorization($this->getAuthorizationToken());

        $thing = $client->getThing(39856, new BoardGameGeekApi\Query\ThingQuery(withStats: true));
        $this->assertInstanceOf(BoardGameGeekApi\Boardgame::class, $thing);
        $this->assertSame('Dixit', $thing->getName());
    }

    /**
     * https://www.boardgamegeek.com/xmlapi2/thing?id=209671,194880
     */
    public function testGetThings(): void
    {
        $client = new Client();
        $client->setAuthorization($this->getAuthorizationToken());

        $things = $client->getThings(
            [ 209671, 194880 ],
            new BoardGameGeekApi\Query\ThingQuery(withStats: true),
        );

        $this->assertCount(2, $things);
        foreach ($things as $thing) {
            $this->assertInstanceOf(Thing::class, $thing);
            $this->assertContains($thing->getName(), [ 'Zona: The Secret of Chernobyl', 'Dream Home' ]);
        }
    }

    public function testGetThingsWithUnknownIds(): void
    {
        $client = new Client();
        $client->setAuthorization($this->getAuthorizationToken());

        $things = $client->getThings(
            [ 111111111111111, 222222222222222 ],
            new BoardGameGeekApi\Query\ThingQuery(withStats: true),
        );
        $this->assertCount(0, $things);
    }

    public function testGetThingsWithEmptyIds(): void
    {
        $client = new Client();
        $client->setAuthorization($this->getAuthorizationToken());

        $this->expectException(InvalidArgumentException::class);
        $things = $client->getThings([], new BoardGameGeekApi\Query\ThingQuery(withStats: true)); // @phpstan-ignore argument.type (Testing the assertion requires passing an empty array)
        $this->assertCount(0, $things);
    }

    /**
     * https://www.boardgamegeek.com/xmlapi2/hot?type=boardgame
     */
    public function testGetHotItems(): void
    {
        $client = new Client();
        $client->setAuthorization($this->getAuthorizationToken());

        $items = $client->getHotItems();

        # empty hot list? error on BGG?
        # $this->assertNotEmpty($items);
        foreach ($items as $i => $item) {
            $this->assertInstanceOf(BoardGameGeekApi\HotItem::class, $item);
            $this->assertSame($i + 1, $item->getRank());
            $this->assertNotEmpty($item->getName());
        }
    }

    /**
     * https://www.boardgamegeek.com/xmlapi2/search/?query=Domek&type=boardgame
     */
    public function testSearch(): void
    {
        $client = new Client();
        $client->setAuthorization($this->getAuthorizationToken());

        $search = $client->search(
            'Domek',
            new BoardGameGeekApi\Query\SearchQuery(
                onlyTypes: [ BoardGameGeekApi\SearchType::BoardGame ],
            ),
        );

        $this->assertGreaterThan(1, count($search));
        foreach ($search as $result) {
            $this->assertNotEmpty($result->getName());
        }
    }

    /**
     * https://www.boardgamegeek.com/xmlapi2/collection?username=nataniel
     */
    public function testGetCollectionWithInvalidUsername(): void
    {
        $client = new Client();
        $client->setAuthorization($this->getAuthorizationToken());

        $this->expectException(BoardGameGeekApi\Exception::class);
        $client->getCollection('notexistingusername');
    }

    public function testGetCollection(): void
    {
        $client = new Client();
        $client->setAuthorization($this->getAuthorizationToken());

        $collection = $client->getCollection('nataniel');
        $this->assertNotEmpty($collection);
        foreach ($collection as $item) {
            $this->assertNotEmpty($item->getName());
            $this->assertStringStartsWith('https://cf.geekdo-images.com', $item->getImage());
        }
    }

    /**
     * https://www.boardgamegeek.com/xmlapi2/plays?username=nataniel
     */
    public function testGetPlays(): void
    {
        $client = new Client();

        $username = $this->getUsername();
        $password = $this->getPassword();

        $client->login($username, $password);

        $plays = $client->getPlaysForUser($username);

        $this->assertNotEmpty($plays);
    }

    /**
     * https://www.boardgamegeek.com/xmlapi2/user?name=nataniel
     */
    public function testGetUserWithInvalidName(): void
    {
        $client = new Client();
        $client->setAuthorization($this->getAuthorizationToken());
        try {
            $client->getUser('notexistingusername');
            $this->fail('Expected InvalidArgumentException for non-existing username.');
        } catch (BoardGameGeekApi\ClientRequestException $clientRequestException) {
            $this->assertSame(404, $clientRequestException->httpCode);
        }
    }

    public function testGetUser(): void
    {
        $client = new Client();
        $client->setAuthorization($this->getAuthorizationToken());

        $item = $client->getUser('nataniel');
        $this->assertInstanceOf(User::class, $item);
        $this->assertSame('Artur', $item->getFirstName());
        $this->assertSame(2004, $item->getYearRegistered());
        $this->assertStringStartsWith('https://cf.geekdo-static.com', $item->getAvatar());
    }

    /**
     * @return non-empty-string
     */
    private function getUsername(): string
    {
        return $this->getEnvironmentVariable('BGG_USERNAME');
    }

    /**
     * @return non-empty-string
     */
    private function getPassword(): string
    {
        return $this->getEnvironmentVariable('BGG_PASSWORD');
    }

    private function getAuthorizationToken(): string
    {
        return 'Bearer ' . $this->getEnvironmentVariable('BGG_AUTH_TOKEN');

    }

    /**
     * @return non-empty-string
     */
    public function getEnvironmentVariable(string $environmentVariable): string
    {
        $value = getenv($environmentVariable);

        if ($value === false || !is_string($value) || $value === '') {
            $this->markTestSkipped('Environment variable ' . $environmentVariable . ' required for this test, is not set');
        }

        return $value;
    }
}
