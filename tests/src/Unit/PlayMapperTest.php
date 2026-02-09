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
        $xml = simplexml_load_file(__DIR__ . '/../../files/play.xml');
        if ($xml === false) {
            self::fail('Could not load XML file');
        }

        $this->play = (new PlaysMapper())->fromXml($xml)->getPlays()[0];
    }

    public function testBasicAttributes(): void
    {
        self::assertSame(102838714, $this->play->getId());
        self::assertEquals(new \DateTimeImmutable('2025-08-19'), $this->play->getDate());
        self::assertSame(1, $this->play->getQuantity());
        self::assertSame(0, $this->play->getLength());
        self::assertFalse($this->play->isIncomplete());
        self::assertFalse($this->play->isNowInStats());
        self::assertSame('Home', $this->play->getLocation());
    }

    public function testItemInfo(): void
    {
        $playItem = $this->play->getItem();

        self::assertSame(155987, $playItem->getObjectId());
        self::assertSame('Abyss', $playItem->getName());
        self::assertEquals([new PlaySubtypeValue('boardgame')], $playItem->getSubtypes());
    }

    public function testComments(): void
    {
        self::assertSame('Played with expansions: - [thing=232197]Abyss: Leviathan[/thing]', $this->play->getComments());
    }

    public function testPlayers(): void
    {
        $players = $this->play->getPlayers();
        self::assertCount(4, $players);

        $p0 = $players[0];
        self::assertInstanceOf(PlayPlayer::class, $p0);
        self::assertSame('andiballone', $p0->getUsername());
        self::assertSame(2919673, $p0->getUserId());
        self::assertSame('Andi', $p0->getName());
        self::assertSame("50", $p0->getScore());
        self::assertFalse($p0->isWin());

        $winner = $players[1];
        self::assertInstanceOf(PlayPlayer::class, $winner);
        self::assertTrue($winner->isWin());
        self::assertSame("82", $winner->getScore());
    }
}
