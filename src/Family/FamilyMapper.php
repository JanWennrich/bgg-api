<?php

namespace JanWennrich\BoardGameGeekApi\Family;

use JanWennrich\BoardGameGeekApi\FamilyType;
use JanWennrich\BoardGameGeekApi\Common\Link;
use JanWennrich\BoardGameGeekApi\Xml;

final class FamilyMapper
{
    /**
     * @return FamilyItem[]
     */
    public function fromXml(\SimpleXMLElement $root): array
    {
        $items = [];
        foreach (Xml::xpath($root, 'item') as $itemNode) {
            $name = null;
            $alternateNames = [];
            foreach (Xml::xpath($itemNode, 'name') as $nameNode) {
                $type = Xml::attrString($nameNode, 'type') ?? '';
                $value = Xml::attrString($nameNode, 'value') ?? '';
                if ($type === 'primary' && $name === null) {
                    $name = $value;
                    continue;
                }

                $alternateNames[] = $value;
            }

            if ($name === null && $alternateNames !== []) {
                $name = array_shift($alternateNames);
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
                $name,
                $alternateNames,
                Xml::childText($itemNode->description ?? null),
                $links,
            );
        }

        return $items;
    }
}
