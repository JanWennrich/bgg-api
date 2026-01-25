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
        $xml = simplexml_load_file(__DIR__ . '/../../files/play.xml') ?: $this->fail('Could not load XML file');

        $this->play = (new PlaysMapper())->fromXml($xml)->getPlays()[0];
    }

    public function testBasicAttributes(): void
    {
        $this->assertSame(102838714, $this->play->getId());
        $this->assertSame('2025-08-19', $this->play->getDate());
        $this->assertSame(1, $this->play->getQuantity());
        $this->assertSame(0, $this->play->getLength());
        $this->assertFalse($this->play->isIncomplete());
        $this->assertFalse($this->play->isNoWinStats());
        $this->assertSame('Home', $this->play->getLocation());
    }

    public function testItemInfo(): void
    {
        $playItem = $this->play->getItem();

        $this->assertSame(155987, $playItem->getObjectId());
        $this->assertSame('Abyss', $playItem->getName());
        $this->assertEquals([new PlaySubtypeValue('boardgame')], $playItem->getSubtypes());
    }

    public function testComments(): void
    {
        $this->assertSame('Played with expansions: - [thing=232197]Abyss: Leviathan[/thing]', $this->play->getComments());
    }

    public function testPlayers(): void
    {
        $players = $this->play->getPlayers();
        $this->assertCount(4, $players);

        $p0 = $players[0];
        $this->assertInstanceOf(PlayPlayer::class, $p0);
        $this->assertSame('andiballone', $p0->getUsername());
        $this->assertSame(2919673, $p0->getUserid());
        $this->assertSame('Andi', $p0->getName());
        $this->assertSame("50", $p0->getScore());
        $this->assertFalse($p0->isWin());

        $winner = $players[1];
        $this->assertInstanceOf(PlayPlayer::class, $winner);
        $this->assertTrue($winner->isWin());
        $this->assertSame("82", $winner->getScore());
    }
}
