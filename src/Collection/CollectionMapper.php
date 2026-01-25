<?php

namespace JanWennrich\BoardGameGeekApi\Collection;

use JanWennrich\BoardGameGeekApi\Common\Link;
use JanWennrich\BoardGameGeekApi\Common\Name;
use JanWennrich\BoardGameGeekApi\Common\Version;
use JanWennrich\BoardGameGeekApi\Xml;

final class CollectionMapper
{
    public function fromXml(\SimpleXMLElement $root): Collection
    {
        $items = [];
        foreach (Xml::xpath($root, 'item') as $itemNode) {
            $nameNode = $itemNode->name ?? null;
            $name = new CollectionName(
                $nameNode ? Xml::childText($nameNode) ?? '' : '',
                $nameNode ? (Xml::attrInt($nameNode, 'sortindex') ?? 0) : 0,
            );

            $stats = $this->mapStats($itemNode->stats ?? null);
            $status = $this->mapStatus($itemNode->status ?? null);
            $privateInfo = $this->mapPrivateInfo($itemNode->privateinfo ?? null);
            $version = $this->mapVersion($itemNode->version ?? null);

            $items[] = new CollectionItem(
                Xml::attrInt($itemNode, 'objectid') ?? 0,
                Xml::attrString($itemNode, 'objecttype') ?? '',
                Xml::attrString($itemNode, 'subtype') ?? '',
                Xml::attrInt($itemNode, 'collid') ?? 0,
                $name,
                Xml::childText($itemNode->originalname ?? null),
                Xml::childText($itemNode->yearpublished ?? null),
                Xml::childText($itemNode->image ?? null),
                Xml::childText($itemNode->thumbnail ?? null),
                $stats,
                $status,
                Xml::childText($itemNode->numplays ?? null) !== null ? (int) $itemNode->numplays : 0,
                $privateInfo,
                $version,
                Xml::childText($itemNode->wantpartslist ?? null),
                Xml::childText($itemNode->haspartslist ?? null),
                Xml::childText($itemNode->wishlistcomment ?? null),
            );
        }

        return new Collection(
            Xml::attrInt($root, 'totalitems') ?? 0,
            Xml::attrString($root, 'pubdate') ?? '',
            $items,
        );
    }

    private function mapStats(?\SimpleXMLElement $statsNode): ?CollectionStats
    {
        if (!$statsNode instanceof \SimpleXMLElement) {
            return null;
        }

        $ratingNode = $statsNode->rating ?? null;
        if ($ratingNode === null) {
            return null;
        }

        $ranks = [];
        foreach (Xml::xpath($ratingNode, 'ranks/rank') as $rankNode) {
            $ranks[] = new CollectionRank(
                Xml::attrString($rankNode, 'type') ?? '',
                Xml::attrInt($rankNode, 'id') ?? 0,
                Xml::attrString($rankNode, 'name') ?? '',
                Xml::attrString($rankNode, 'friendlyname') ?? '',
                Xml::attrString($rankNode, 'value') ?? '',
                Xml::attrString($rankNode, 'bayesaverage') ?? '',
            );
        }

        $collectionRating = new CollectionRating(
            Xml::attrString($ratingNode, 'value') ?? '',
            Xml::childIntValue($ratingNode, 'usersrated') ?? 0,
            Xml::childFloatValue($ratingNode, 'average') ?? 0.0,
            Xml::childFloatValue($ratingNode, 'bayesaverage') ?? 0.0,
            Xml::childFloatValue($ratingNode, 'stddev') ?? 0.0,
            Xml::childFloatValue($ratingNode, 'median') ?? 0.0,
            $ranks,
        );

        return new CollectionStats(
            Xml::attrInt($statsNode, 'minplayers') ?? 0,
            Xml::attrInt($statsNode, 'maxplayers') ?? 0,
            Xml::attrInt($statsNode, 'minplaytime') ?? 0,
            Xml::attrInt($statsNode, 'maxplaytime') ?? 0,
            Xml::attrInt($statsNode, 'playingtime') ?? 0,
            Xml::attrInt($statsNode, 'numowned') ?? 0,
            $collectionRating,
        );
    }

    private function mapStatus(?\SimpleXMLElement $statusNode): CollectionStatus
    {
        $statusNode ??= new \SimpleXMLElement('<status/>');

        return new CollectionStatus(
            Xml::attrBool($statusNode, 'own') ?? false,
            Xml::attrBool($statusNode, 'prevowned') ?? false,
            Xml::attrBool($statusNode, 'fortrade') ?? false,
            Xml::attrBool($statusNode, 'want') ?? false,
            Xml::attrBool($statusNode, 'wanttoplay') ?? false,
            Xml::attrBool($statusNode, 'wanttobuy') ?? false,
            Xml::attrBool($statusNode, 'wishlist') ?? false,
            Xml::attrBool($statusNode, 'preordered') ?? false,
            Xml::attrString($statusNode, 'lastmodified') ?? '',
        );
    }

    private function mapPrivateInfo(?\SimpleXMLElement $node): ?CollectionPrivateInfo
    {
        if (!$node instanceof \SimpleXMLElement) {
            return null;
        }

        return new CollectionPrivateInfo(
            Xml::childText($node->privatecomment ?? null),
            Xml::attrString($node, 'pp_currency'),
            Xml::attrFloat($node, 'pricepaid'),
            Xml::attrString($node, 'cv_currency'),
            Xml::attrFloat($node, 'currvalue'),
            Xml::attrInt($node, 'quantity'),
            Xml::attrString($node, 'acquisitiondate'),
            Xml::attrString($node, 'acquiredfrom'),
            Xml::attrString($node, 'inventorylocation'),
        );
    }

    private function mapVersion(?\SimpleXMLElement $node): ?CollectionVersion
    {
        if (!$node instanceof \SimpleXMLElement) {
            return null;
        }

        $publisherNode = $node->publisher ?? null;
        $publisher = null;
        if ($publisherNode !== null) {
            $publisher = new CollectionVersionPublisher(
                Xml::childText($publisherNode) ?? '',
                Xml::attrInt($publisherNode, 'publisherid') ?? 0,
            );
        }

        $itemNode = $node->item ?? null;
        $item = null;
        if ($itemNode !== null) {
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

            $item = new Version(
                Xml::attrInt($itemNode, 'id') ?? 0,
                Xml::attrString($itemNode, 'type') ?? '',
                Xml::childText($itemNode->thumbnail ?? null),
                Xml::childText($itemNode->image ?? null),
                $names,
                Xml::childIntValue($itemNode, 'yearpublished'),
                $links,
                Xml::childStringValue($itemNode, 'productcode'),
                Xml::childFloatValue($itemNode, 'width'),
                Xml::childFloatValue($itemNode, 'length'),
                Xml::childFloatValue($itemNode, 'depth'),
                Xml::childFloatValue($itemNode, 'weight'),
            );
        }

        return new CollectionVersion(
            Xml::childIntValue($node, 'imageid'),
            Xml::childIntValue($node, 'year'),
            $publisher,
            Xml::childText($node->other ?? null),
            Xml::childText($node->barcode ?? null),
            $item,
        );
    }
}
