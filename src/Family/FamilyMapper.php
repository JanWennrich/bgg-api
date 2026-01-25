<?php

namespace JanWennrich\BoardGameGeekApi\Family;

use JanWennrich\BoardGameGeekApi\FamilyType;
use JanWennrich\BoardGameGeekApi\Common\Link;
use JanWennrich\BoardGameGeekApi\Common\Name;
use JanWennrich\BoardGameGeekApi\Xml;

final class FamilyMapper
{
    public function fromXml(\SimpleXMLElement $root): FamilyItems
    {
        $items = [];
        foreach (Xml::xpath($root, 'item') as $itemNode) {
            $names = [];
            foreach (Xml::xpath($itemNode, 'name') as $nameNode) {
                $names[] = new Name(
                    Xml::attrString($nameNode, 'type') ?? '',
                    Xml::attrInt($nameNode, 'sortindex') ?? 0,
                    Xml::attrString($nameNode, 'value') ?? '',
                );
            }

            $links = [];
            foreach (Xml::xpath($itemNode, 'link') as $linkNode) {
                $links[] = new Link(
                    Xml::attrString($linkNode, 'type') ?? '',
                    Xml::attrInt($linkNode, 'id') ?? 0,
                    Xml::attrString($linkNode, 'value') ?? '',
                    Xml::attrBool($linkNode, 'inbound'),
                );
            }

            $items[] = new FamilyItem(
                Xml::attrInt($itemNode, 'id') ?? 0,
                FamilyType::tryFrom(Xml::attrString($itemNode, 'type') ?? ''),
                Xml::childText($itemNode->thumbnail ?? null),
                Xml::childText($itemNode->image ?? null),
                $names,
                Xml::childText($itemNode->description ?? null),
                $links,
            );
        }

        return new FamilyItems(
            $items,
        );
    }
}
