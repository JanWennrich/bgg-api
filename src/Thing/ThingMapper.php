<?php

namespace JanWennrich\BoardGameGeekApi\Thing;

use JanWennrich\BoardGameGeekApi\ThingType;
use JanWennrich\BoardGameGeekApi\Common\Link;
use JanWennrich\BoardGameGeekApi\Common\Name;
use JanWennrich\BoardGameGeekApi\Common\Rank;
use JanWennrich\BoardGameGeekApi\Common\Ranks;
use JanWennrich\BoardGameGeekApi\Common\Ratings;
use JanWennrich\BoardGameGeekApi\Common\Statistics;
use JanWennrich\BoardGameGeekApi\Common\Video;
use JanWennrich\BoardGameGeekApi\Common\Videos;
use JanWennrich\BoardGameGeekApi\Common\Version;
use JanWennrich\BoardGameGeekApi\Xml;

final class ThingMapper
{
    public function fromXml(\SimpleXMLElement $item): Thing
    {
        $names = $this->mapNames(Xml::xpath($item, 'name'));
        $links = $this->mapLinks(Xml::xpath($item, 'link'));
        $polls = $this->mapPolls(Xml::xpath($item, 'poll'));
        $versions = $this->mapVersions($item->versions ?? null);
        $statistics = $this->mapStatistics($item->statistics ?? null);
        $listings = $this->mapMarketplaceListings($item->marketplacelistings ?? null);

        return new Thing(
            (int) $item['id'],
            ThingType::tryFrom((string) $item['type']),
            Xml::childText($item->thumbnail ?? null),
            Xml::childText($item->image ?? null),
            $names,
            Xml::childText($item->description ?? null),
            Xml::childIntValue($item, 'yearpublished'),
            Xml::childStringValue($item, 'datepublished'),
            Xml::childIntValue($item, 'issueindex'),
            Xml::childIntValue($item, 'minplayers'),
            Xml::childIntValue($item, 'maxplayers'),
            Xml::childStringValue($item, 'releasedate'),
            Xml::childStringValue($item, 'seriescode'),
            Xml::childIntValue($item, 'playingtime'),
            Xml::childIntValue($item, 'minplaytime'),
            Xml::childIntValue($item, 'maxplaytime'),
            Xml::childIntValue($item, 'minage'),
            $links,
            $polls,
            $this->mapVideos($item->videos ?? null),
            $versions,
            $this->mapComments($item->comments ?? null),
            $statistics,
            $listings,
        );
    }

    /**
     * @param \SimpleXMLElement[] $nodes
     * @return Name[]
     */
    private function mapNames(array $nodes): array
    {
        $names = [];
        foreach ($nodes as $node) {
            $type = Xml::attrString($node, 'type') ?? '';
            $sortIndex = Xml::attrInt($node, 'sortindex') ?? 0;
            $value = Xml::attrString($node, 'value') ?? '';
            $names[] = new Name($type, $sortIndex, $value);
        }

        return $names;
    }

    /**
     * @param \SimpleXMLElement[] $nodes
     * @return Link[]
     */
    private function mapLinks(array $nodes): array
    {
        $links = [];
        foreach ($nodes as $node) {
            $type = Xml::attrString($node, 'type') ?? '';
            $id = Xml::attrInt($node, 'id') ?? 0;
            $value = Xml::attrString($node, 'value') ?? '';
            $inbound = Xml::attrBool($node, 'inbound');
            $links[] = new Link($type, $id, $value, $inbound);
        }

        return $links;
    }

    /**
     * @param \SimpleXMLElement[] $nodes
     * @return Poll[]
     */
    private function mapPolls(array $nodes): array
    {
        $polls = [];
        foreach ($nodes as $node) {
            $results = [];
            foreach (Xml::xpath($node, 'results') as $resultsNode) {
                $resultItems = [];
                foreach (Xml::xpath($resultsNode, 'result') as $resultNode) {
                    $resultItems[] = new PollResult(
                        Xml::attrString($resultNode, 'value') ?? '',
                        Xml::attrInt($resultNode, 'numvotes') ?? 0,
                        Xml::attrInt($resultNode, 'level'),
                    );
                }

                $results[] = new PollResults(
                    Xml::attrString($resultsNode, 'numplayers'),
                    $resultItems,
                );
            }

            $polls[] = new Poll(
                Xml::attrString($node, 'name') ?? '',
                Xml::attrString($node, 'title') ?? '',
                Xml::attrInt($node, 'totalvotes') ?? 0,
                $results,
            );
        }

        return $polls;
    }

    private function mapVideos(?\SimpleXMLElement $videosNode): ?Videos
    {
        if (!$videosNode instanceof \SimpleXMLElement) {
            return null;
        }

        $total = Xml::attrInt($videosNode, 'total');
        if ($total === null) {
            return null;
        }

        $videos = [];
        foreach (Xml::xpath($videosNode, 'video') as $videoNode) {
            $videos[] = new Video(
                Xml::attrInt($videoNode, 'id') ?? 0,
                Xml::attrString($videoNode, 'title') ?? '',
                Xml::attrString($videoNode, 'category') ?? '',
                Xml::attrString($videoNode, 'language') ?? '',
                Xml::attrString($videoNode, 'link') ?? '',
                Xml::attrString($videoNode, 'username') ?? '',
                Xml::attrInt($videoNode, 'userid') ?? 0,
                Xml::attrString($videoNode, 'postdate') ?? '',
            );
        }

        return new Videos($total, $videos);
    }

    /**
     * @return Version[]
     */
    private function mapVersions(?\SimpleXMLElement $versionsNode): array
    {
        if (!$versionsNode instanceof \SimpleXMLElement) {
            return [];
        }

        $nodes = Xml::xpath($versionsNode, 'item');
        $versions = [];
        foreach ($nodes as $node) {
            $names = $this->mapNames(Xml::xpath($node, 'name'));

            $links = [];
            foreach (Xml::xpath($node, 'link') as $linkNode) {
                $links[] = new Link(
                    Xml::attrString($linkNode, 'type') ?? '',
                    Xml::attrInt($linkNode, 'id') ?? 0,
                    Xml::attrString($linkNode, 'value') ?? '',
                    Xml::attrBool($linkNode, 'inbound'),
                );
            }

            $versions[] = new Version(
                Xml::attrInt($node, 'id') ?? 0,
                Xml::attrString($node, 'type') ?? '',
                Xml::childText($node->thumbnail ?? null),
                Xml::childText($node->image ?? null),
                $names,
                Xml::childIntValue($node, 'yearpublished'),
                $links,
                Xml::childStringValue($node, 'productcode'),
                Xml::childFloatValue($node, 'width'),
                Xml::childFloatValue($node, 'length'),
                Xml::childFloatValue($node, 'depth'),
                Xml::childFloatValue($node, 'weight'),
            );
        }

        return $versions;
    }

    private function mapComments(?\SimpleXMLElement $commentsNode): ?Comments
    {
        if (!$commentsNode instanceof \SimpleXMLElement) {
            return null;
        }

        $page = Xml::attrString($commentsNode, 'page');
        $totalItems = Xml::attrInt($commentsNode, 'totalitems');
        if ($page === null || $totalItems === null) {
            return null;
        }

        $comments = [];
        foreach (Xml::xpath($commentsNode, 'comment') as $commentNode) {
            $comments[] = new Comment(
                Xml::attrString($commentNode, 'username') ?? '',
                Xml::attrString($commentNode, 'rating') ?? '',
                Xml::attrString($commentNode, 'value') ?? '',
            );
        }

        return new Comments($page, $totalItems, $comments);
    }

    private function mapStatistics(?\SimpleXMLElement $statisticsNode): ?Statistics
    {
        if (!$statisticsNode instanceof \SimpleXMLElement) {
            return null;
        }

        $page = Xml::attrString($statisticsNode, 'page');
        if ($page === null) {
            return null;
        }

        $ratings = [];
        foreach (Xml::xpath($statisticsNode, 'ratings') as $node) {
            $ranks = [];
            foreach (Xml::xpath($node, 'ranks/rank') as $rankNode) {
                $ranks[] = new Rank(
                    Xml::attrString($rankNode, 'type') ?? '',
                    Xml::attrInt($rankNode, 'id') ?? 0,
                    Xml::attrString($rankNode, 'name') ?? '',
                    Xml::attrString($rankNode, 'friendlyname') ?? '',
                    Xml::attrInt($rankNode, 'value') ?? 0,
                    Xml::attrFloat($rankNode, 'bayesaverage') ?? 0.0,
                );
            }

            $ratings[] = new Ratings(
                Xml::attrString($node, 'date'),
                Xml::childIntValue($node, 'usersrated') ?? 0,
                Xml::childFloatValue($node, 'average') ?? 0.0,
                Xml::childFloatValue($node, 'bayesaverage') ?? 0.0,
                Xml::childFloatValue($node, 'stddev'),
                Xml::childFloatValue($node, 'median'),
                Xml::childIntValue($node, 'owned') ?? 0,
                Xml::childIntValue($node, 'trading') ?? 0,
                Xml::childIntValue($node, 'wanting') ?? 0,
                Xml::childIntValue($node, 'wishing') ?? 0,
                Xml::childIntValue($node, 'numcomments') ?? 0,
                Xml::childIntValue($node, 'numweights') ?? 0,
                Xml::childFloatValue($node, 'averageweight') ?? 0.0,
                $ranks === [] ? null : new Ranks($ranks),
            );
        }

        return new Statistics($page, $ratings);
    }

    /**
     * @return Listing[]
     */
    private function mapMarketplaceListings(?\SimpleXMLElement $marketplaceNode): array
    {
        if (!$marketplaceNode instanceof \SimpleXMLElement) {
            return [];
        }

        $nodes = Xml::xpath($marketplaceNode, 'listing');
        $listings = [];
        foreach ($nodes as $node) {
            $priceNode = $node->price ?? null;
            $price = new ListingPrice(
                $priceNode ? (Xml::attrString($priceNode, 'currency') ?? '') : '',
                $priceNode ? (Xml::attrFloat($priceNode, 'value') ?? 0.0) : 0.0,
            );

            $linkNode = $node->link ?? null;
            $link = new ListingLink(
                $linkNode ? (Xml::attrString($linkNode, 'href') ?? '') : '',
                $linkNode ? (Xml::attrString($linkNode, 'title') ?? '') : '',
            );

            $listings[] = new Listing(
                Xml::childStringValue($node, 'listdate') ?? '',
                $price,
                Xml::childStringValue($node, 'condition') ?? '',
                Xml::childStringValue($node, 'notes') ?? '',
                $link,
            );
        }

        return $listings;
    }
}
