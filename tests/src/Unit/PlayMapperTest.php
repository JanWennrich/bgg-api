<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Test\Unit;

use JanWennrich\BoardGameGeekApi\Plays\Play;
use JanWennrich\BoardGameGeekApi\Plays\PlayPlayer;
use JanWennrich\BoardGameGeekApi\Plays\PlaysMapper;
use JanWennrich\BoardGameGeekApi\Plays\PlaySubtypeValue;
use PHPUnit\Framework\TestCase;

final class PlayMapperTest extends TestCase
{
    private Play $play;

    protected function setUp(): void
    {
        $xml = simplexml_load_file(__DIR__ . '/../../fixtures/plays/plays-id=418059+type=thing.xml');
        if ($xml === false) {
            self::fail('Could not load XML file');
        }

        $plays = (new PlaysMapper())->fromXml($xml)->getPlays();
        $play = null;
        foreach ($plays as $candidate) {
            if ($candidate->getId() === 106196078) {
                $play = $candidate;
                break;
            }
        }

        if (!$play instanceof Play) {
            self::fail('Expected to find play id=106196078 in plays-id=418059+type=thing.xml');
        }

        $this->play = $play;
    }

    public function testBasicAttributes(): void
    {
        self::assertSame(106196078, $this->play->getId());
        self::assertEquals(new \DateTimeImmutable('2568-11-19'), $this->play->getDate());
        self::assertSame(1, $this->play->getQuantity());
        self::assertSame(210, $this->play->getLength());
        self::assertFalse($this->play->isIncomplete());
        self::assertFalse($this->play->isNowInStats());
        self::assertSame('Home 166', $this->play->getLocation());
    }

    public function testItemInfo(): void
    {
        $playItem = $this->play->getItem();

        self::assertSame(418059, $playItem->getObjectId());
        self::assertSame('SETI: Search for Extraterrestrial Intelligence', $playItem->getName());
        self::assertEquals([new PlaySubtypeValue('boardgame')], $playItem->getSubtypes());
    }

    public function testComments(): void
    {
        self::assertNotEmpty($this->play->getComments());
    }

    public function testPlayers(): void
    {
        $players = $this->play->getPlayers();
        self::assertCount(2, $players);

        $p0 = $players[0];
        self::assertInstanceOf(PlayPlayer::class, $p0);
        self::assertSame('Eternal_BB', $p0->getUsername());
        self::assertSame(3572535, $p0->getUserId());
        self::assertSame('BB', $p0->getName());
        self::assertSame("255", $p0->getScore());
        self::assertTrue($p0->isWin());

        $winner = $players[1];
        self::assertInstanceOf(PlayPlayer::class, $winner);
        self::assertFalse($winner->isWin());
        self::assertSame("230", $winner->getScore());
    }
}
