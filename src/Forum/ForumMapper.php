<?php

namespace JanWennrich\BoardGameGeekApi\Forum;

use JanWennrich\BoardGameGeekApi\Xml;

final class ForumMapper
{
    public function fromXml(\SimpleXMLElement $root): Forum
    {
        $threadsNode = $root->threads ?? null;
        $threads = [];
        if ($threadsNode !== null) {
            foreach (Xml::xpath($threadsNode, 'thread') as $threadNode) {
                $threads[] = new ForumThread(
                    Xml::attrInt($threadNode, 'id') ?? 0,
                    Xml::attrString($threadNode, 'subject') ?? '',
                    Xml::attrString($threadNode, 'author') ?? '',
                    Xml::attrInt($threadNode, 'numarticles') ?? 0,
                    Xml::attrString($threadNode, 'postdate') ?? '',
                    Xml::attrString($threadNode, 'lastpostdate') ?? '',
                );
            }
        }

        return new Forum(
            Xml::attrInt($root, 'id') ?? 0,
            Xml::attrString($root, 'title') ?? '',
            Xml::attrInt($root, 'numthreads') ?? 0,
            Xml::attrInt($root, 'numposts') ?? 0,
            Xml::attrString($root, 'lastpostdate') ?? '',
            Xml::attrBool($root, 'noposting') ?? false,
            $threads,
        );
    }
}
