<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Test;

use JanWennrich\BoardGameGeekApi\Client;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;
use JanWennrich\BoardGameGeekApi;

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
    public function testGetThing()
    {
        $this->markTestIncomplete('Test requires authorization token');

        // @phpstan-ignore deadCode.unreachable
        $client = new Client();
        $thing = $client->getThing(5371611111, true);
        $this->assertNull($thing);

        $thing = $client->getThing(39856, true);
        $this->assertInstanceOf(BoardGameGeekApi\Boardgame::class, $thing);
        $this->assertEquals('Dixit', $thing->getName());
    }

    /**
     * https://www.boardgamegeek.com/xmlapi2/thing?id=209671,194880
     */
    public function testGetThings()
    {
        $this->markTestIncomplete('Test requires authorization token');

        // @phpstan-ignore deadCode.unreachable
        $client = new Client();
        $things = $client->getThings([ 209671, 194880 ], true);

        $this->assertCount(2, $things);
        foreach ($things as $thing) {
            $this->assertInstanceOf(BoardGameGeekApi\Thing::class, $thing);
            $this->assertContains($thing->getName(), [ 'Zona: The Secret of Chernobyl', 'Dream Home' ]);
        }

        $things = $client->getThings([ '111111111111111111111', '222222222222222222' ], true);
        $this->assertCount(0, $things);

        $things = $client->getThings([], true);
        $this->assertCount(0, $things);
    }

    /**
     * https://www.boardgamegeek.com/xmlapi2/hot?type=boardgame
     */
    public function testGetHotItems()
    {
        $this->markTestIncomplete('Test requires authorization token');

        // @phpstan-ignore deadCode.unreachable
        $client = new Client();
        $items = $client->getHotItems();

        # empty hot list? error on BGG?
        # $this->assertNotEmpty($items);
        foreach ($items as $i => $item) {
            $this->assertInstanceOf(BoardGameGeekApi\HotItem::class, $item);
            $this->assertEquals($i + 1, $item->getRank());
            $this->assertNotEmpty($item->getName());
        }
    }

    /**
     * https://www.boardgamegeek.com/xmlapi2/search/?query=Domek&type=boardgame
     */
    public function testSearch()
    {
        $this->markTestIncomplete('Test requires authorization token');

        // @phpstan-ignore deadCode.unreachable
        $client = new Client();
        $search = $client->search('Domek', false, BoardGameGeekApi\Type::BOARDGAME);

        $this->assertGreaterThan(1, count($search));
        foreach ($search as $result) {
            $this->assertInstanceOf(BoardGameGeekApi\Search\Result::class, $result);
            $this->assertNotEmpty($result->getName());
        }
    }

    /**
     * https://www.boardgamegeek.com/xmlapi2/collection?username=nataniel
     */
    public function testGetCollection()
    {
        $client = new Client();
        $this->expectException(BoardGameGeekApi\Exception::class);
        $client->getCollection([ 'username' => 'notexistingusername' ]);

        $items = $client->getCollection([ 'username' => 'nataniel' ]);
        $this->assertNotEmpty($items);
        foreach ($items as $i => $item) {
            $this->assertNotEmpty($item->getName());
            $this->assertStringStartsWith('https://cf.geekdo-images.com', $item->getImage());
        }
    }

    /**
     * https://www.boardgamegeek.com/xmlapi2/plays?username=nataniel
     */
    public function testGetPlays()
    {
        $client = new Client();

        $username = $this->getUsername();
        $password = $this->getPassword();

        $client->login($username, $password);

        $plays = $client->getPlays(['username' => $username]);

        $this->assertNotEmpty($plays);
    }

    /**
     * https://www.boardgamegeek.com/xmlapi2/user?name=nataniel
     */
    public function testGetUser()
    {
        $this->markTestIncomplete('Test requires authorization token');

        // @phpstan-ignore deadCode.unreachable
        $client = new Client();
        $item = $client->getUser('notexistingusername');
        $this->assertNull($item);

        $item = $client->getUser('nataniel');
        $this->assertInstanceOf(BoardGameGeekApi\User::class, $item);
        $this->assertEquals('Artur', $item->getFirstName());
        $this->assertEquals('2004', $item->getYearRegistered());
        $this->assertStringStartsWith('https://cf.geekdo-static.com', $item->getAvatar());
    }

    private function getUsername(): string
    {
        return $this->getEnvironmentVariable('BGG_USERNAME');
    }

    private function getPassword(): string
    {
        return $this->getEnvironmentVariable('BGG_PASSWORD');
    }

    public function getEnvironmentVariable(string $environmentVariable): string
    {
        $value = getenv($environmentVariable);

        $this->assertNotFalse($value, "Environment variable '$environmentVariable' must be set");
        $this->assertIsString($value, "Environment variable '$environmentVariable' must be a string");
        $this->assertNotEmpty($value, "Environment variable '$environmentVariable' must not be empty");

        return $value;
    }
}
