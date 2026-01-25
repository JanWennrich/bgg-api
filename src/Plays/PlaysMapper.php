<?php

namespace JanWennrich\BoardGameGeekApi\Plays;

use JanWennrich\BoardGameGeekApi\Xml;

final class PlaysMapper
{
    public function fromXml(\SimpleXMLElement $root): Plays
    {
        $plays = [];
        foreach (Xml::xpath($root, 'play') as $playNode) {
            $itemNode = $playNode->item ?? null;
            $subtypesNode = $itemNode?->subtypes;
            $subtypes = [];
            if ($subtypesNode !== null) {
                foreach (Xml::xpath($subtypesNode, 'subtype') as $subtypeNode) {
                    $subtypes[] = new PlaySubtypeValue(
                        Xml::attrString($subtypeNode, 'value') ?? '',
                    );
                }
            }

            $item = new PlayItem(
                $itemNode ? (Xml::attrString($itemNode, 'name') ?? '') : '',
                $itemNode ? (Xml::attrString($itemNode, 'objecttype') ?? '') : '',
                $itemNode ? (Xml::attrInt($itemNode, 'objectid') ?? 0) : 0,
                $subtypes,
            );

            $playersNode = $playNode->players ?? null;
            $players = [];
            if ($playersNode !== null) {
                foreach (Xml::xpath($playersNode, 'player') as $playerNode) {
                    $players[] = new PlayPlayer(
                        Xml::attrString($playerNode, 'username') ?? '',
                        Xml::attrInt($playerNode, 'userid') ?? 0,
                        Xml::attrString($playerNode, 'name') ?? '',
                        Xml::attrString($playerNode, 'startposition') ?? '',
                        Xml::attrString($playerNode, 'color') ?? '',
                        Xml::attrString($playerNode, 'score') ?? '',
                        Xml::attrBool($playerNode, 'new') ?? false,
                        Xml::attrInt($playerNode, 'rating') ?? 0,
                        Xml::attrBool($playerNode, 'win') ?? false,
                    );
                }
            }

            $plays[] = new Play(
                Xml::attrInt($playNode, 'id') ?? 0,
                Xml::attrString($playNode, 'date') ?? '',
                Xml::attrInt($playNode, 'quantity') ?? 0,
                Xml::attrInt($playNode, 'length') ?? 0,
                Xml::attrBool($playNode, 'incomplete') ?? false,
                Xml::attrBool($playNode, 'nowinstats') ?? false,
                Xml::attrString($playNode, 'location') ?? '',
                $item,
                Xml::childText($playNode->comments ?? null),
                $players,
            );
        }

        return new Plays(
            Xml::attrString($root, 'username') ?? '',
            Xml::attrInt($root, 'userid') ?? 0,
            Xml::attrInt($root, 'total') ?? 0,
            Xml::attrInt($root, 'page') ?? 0,
            $plays,
        );
    }
}
