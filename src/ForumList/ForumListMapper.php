<?php

namespace JanWennrich\BoardGameGeekApi\ForumList;

use JanWennrich\BoardGameGeekApi\ItemType;
use JanWennrich\BoardGameGeekApi\Xml;

final class ForumListMapper
{
    public function fromXml(\SimpleXMLElement $root): ForumList
    {
        $forums = [];
        foreach (Xml::xpath($root, 'forum') as $forumNode) {
            $forums[] = new ForumListEntry(
                Xml::attrInt($forumNode, 'id') ?? 0,
                Xml::attrInt($forumNode, 'groupid') ?? 0,
                Xml::attrString($forumNode, 'title') ?? '',
                Xml::attrBool($forumNode, 'noposting') ?? false,
                Xml::attrString($forumNode, 'description') ?? '',
                Xml::attrInt($forumNode, 'numthreads') ?? 0,
                Xml::attrInt($forumNode, 'numposts') ?? 0,
                Xml::attrString($forumNode, 'lastpostdate') ?? '',
            );
        }

        return new ForumList(
            ItemType::tryFrom(Xml::attrString($root, 'type') ?? ''),
            Xml::attrInt($root, 'id') ?? 0,
            $forums,
        );
    }
}
