<?php

namespace JanWennrich\BoardGameGeekApi\Thread;

use JanWennrich\BoardGameGeekApi\Xml;

final class ThreadMapper
{
    public function fromXml(\SimpleXMLElement $root): Thread
    {
        $articlesNode = $root->articles ?? null;
        $articles = [];
        if ($articlesNode !== null) {
            foreach (Xml::xpath($articlesNode, 'article') as $articleNode) {
                $articles[] = new ThreadArticle(
                    Xml::attrInt($articleNode, 'id') ?? 0,
                    Xml::attrString($articleNode, 'username') ?? '',
                    Xml::attrString($articleNode, 'link') ?? '',
                    Xml::attrString($articleNode, 'postdate') ?? '',
                    Xml::attrString($articleNode, 'editdate') ?? '',
                    Xml::attrInt($articleNode, 'numedits') ?? 0,
                    Xml::childText($articleNode->subject ?? null) ?? '',
                    Xml::childText($articleNode->body ?? null) ?? '',
                );
            }
        }

        return new Thread(
            Xml::attrInt($root, 'id') ?? 0,
            Xml::attrInt($root, 'numarticles') ?? 0,
            Xml::attrString($root, 'link') ?? '',
            Xml::childText($root->subject ?? null) ?? '',
            $articles,
        );
    }
}
