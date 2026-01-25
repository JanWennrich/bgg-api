<?php

namespace JanWennrich\BoardGameGeekApi\Hot;

use JanWennrich\BoardGameGeekApi\Xml;

final class HotMapper
{
    /**
     * @return HotItem[]
     */
    public function fromXml(\SimpleXMLElement $root): array
    {
        $items = [];
        foreach (Xml::xpath($root, 'item') as $itemNode) {
            $items[] = new HotItem(
                Xml::attrInt($itemNode, 'id') ?? 0,
                Xml::attrInt($itemNode, 'rank') ?? 0,
                Xml::childStringValue($itemNode, 'thumbnail'),
                Xml::childStringValue($itemNode, 'name'),
                Xml::childIntValue($itemNode, 'yearpublished'),
            );
        }

        return $items;
    }
}
