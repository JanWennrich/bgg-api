<?php

namespace JanWennrich\BoardGameGeekApi\Boardgame;

use JanWennrich\BoardGameGeekApi\Exception;

abstract class Link
{
    public const TYPE_CATEGORY = 'boardgamecategory';
    public const TYPE_MECHANIC = 'boardgamemechanic';
    public const TYPE_EXPANSION = 'boardgameexpansion';
    public const TYPE_DESIGNER = 'boardgamedesigner';
    public const TYPE_ARTIST = 'boardgameartist';
    public const TYPE_PUBLISHER = 'boardgamepublisher';
    public const TYPE_VERSION = 'boardgameversion';

    /** @var \SimpleXMLElement */
    protected $root;

    public function __construct(\SimpleXMLElement $xml)
    {
        $this->root = $xml;
    }

    public function getId(): int
    {
        return (int) $this->root['id'];
    }

    public function getType(): int
    {
        return (int) $this->root['type'];
    }

    public function getName(): string
    {
        return (string) $this->root['value'];
    }

    public function toString(): string
    {
        return $this->getName();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @throws Exception
     */
    public static function factory(\SimpleXMLElement $xml): Link
    {
        switch ($xml['type']) {
            case self::TYPE_ARTIST:    return new Artist($xml);
            case self::TYPE_DESIGNER:  return new Designer($xml);
            case self::TYPE_PUBLISHER: return new Publisher($xml);
            case self::TYPE_EXPANSION: return new Expansion($xml);
            case self::TYPE_CATEGORY:  return new Category($xml);
            case self::TYPE_MECHANIC:  return new Mechanic($xml);
            case self::TYPE_VERSION:   return new Version($xml);
            default:
                throw new Exception(sprintf('Invalid link type: %s.', $xml['type']));
        }
    }
}
