<?php

namespace JanWennrich\BoardGameGeekApi\Test;

use PHPUnit\Framework\TestCase;
use JanWennrich\BoardGameGeekApi;

class ClientTest extends TestCase
{
    /**
     * https://boardgamegeek.com/boardgame/39856/dixit
     * @covers BoardGameGeekApi\Client::getThing
     */
    public function testGetThing()
    {
        $client = new BoardGameGeekApi\Client();
        $thing = $client->getThing(5371611111, true);
        $this->assertNull($thing);

        $thing = $client->getThing(39856, true);
        $this->assertInstanceOf(BoardGameGeekApi\Boardgame::class, $thing);
        $this->assertEquals('Dixit', $thing->getName());
    }

    /**
     * https://www.boardgamegeek.com/xmlapi2/thing?id=209671,194880
     * @covers BoardGameGeekApi\Client::getThing
     */
    public function testGetThings()
    {
        $client = new BoardGameGeekApi\Client();
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
     * @covers BoardGameGeekApi\Client::getHotItems
     */
    public function testGetHotItems()
    {
        $client = new BoardGameGeekApi\Client();
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
     * @covers BoardGameGeekApi\Client::search
     */
    public function testSearch()
    {
        $client = new BoardGameGeekApi\Client();
        $search = $client->search('Domek', false, BoardGameGeekApi\Type::BOARDGAME);

        $this->assertInstanceOf(BoardGameGeekApi\Search\Query::class, $search);
        $this->assertGreaterThan(1, count($search));
        foreach ($search as $result) {
            $this->assertInstanceOf(BoardGameGeekApi\Search\Result::class, $result);
            $this->assertNotEmpty($result->getName());
        }
    }

    /**
     * https://www.boardgamegeek.com/xmlapi2/collection?username=nataniel
     * @covers BoardGameGeekApi\Client::getCollection
     */
    public function testGetCollection()
    {
        $client = new BoardGameGeekApi\Client();
        $this->expectException(BoardGameGeekApi\Exception::class);
        $client->getCollection([ 'username' => 'notexistingusername' ]);

        $items = $client->getCollection([ 'username' => 'nataniel' ]);
        $this->assertInstanceOf(BoardGameGeekApi\Collection::class, $items);
        $this->assertNotEmpty($items);
        foreach ($items as $i => $item) {
            $this->assertInstanceOf(BoardGameGeekApi\Collection\Item::class, $item);
            $this->assertNotEmpty($item->getName());
            $this->assertStringStartsWith('https://cf.geekdo-images.com', $item->getImage());
        }
    }

    /**
     * https://www.boardgamegeek.com/xmlapi2/plays?username=nataniel
     * @covers BoardGameGeekApi\Client::getPlays
     */
    public function testGetPlays()
    {
        $client = new BoardGameGeekApi\Client();

        $username = $this->getUsername();
        $password = $this->getPassword();

        $client->login($username, $password);

        $plays = $client->getPlays(['username' => $username]);

        $this->assertNotEmpty($plays);

        foreach ($plays as $play) {
            $this->assertInstanceOf(BoardGameGeekApi\Play::class, $play);
        }
    }

    /**
     * https://www.boardgamegeek.com/xmlapi2/user?name=nataniel
     * @covers BoardGameGeekApi\Client::getUser
     */
    public function testGetUser()
    {
        $client = new BoardGameGeekApi\Client();
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
