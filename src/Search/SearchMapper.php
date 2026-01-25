<?php

namespace JanWennrich\BoardGameGeekApi\Search;

use JanWennrich\BoardGameGeekApi\SearchType;
use JanWennrich\BoardGameGeekApi\Xml;

final class SearchMapper
{
    public function fromXml(\SimpleXMLElement $root): Search
    {
        $items = [];
        foreach (Xml::xpath($root, 'item') as $itemNode) {
            $nameNode = $itemNode->name ?? null;
            $name = new SearchName(
                $nameNode ? (Xml::attrString($nameNode, 'type') ?? '') : '',
                $nameNode ? (Xml::attrString($nameNode, 'value') ?? '') : '',
            );

            $items[] = new SearchResult(
                Xml::attrInt($itemNode, 'id') ?? 0,
                SearchType::tryFrom(Xml::attrString($itemNode, 'type') ?? ''),
                $name,
                Xml::childIntValue($itemNode, 'yearpublished'),
            );
        }

        return new Search(
            Xml::attrInt($root, 'total') ?? 0,
            $items,
        );
    }
}
