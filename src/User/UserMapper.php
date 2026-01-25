<?php

namespace JanWennrich\BoardGameGeekApi\User;

use JanWennrich\BoardGameGeekApi\Xml;

final class UserMapper
{
    public function fromXml(\SimpleXMLElement $root): User
    {
        $buddies = $this->mapBuddies($root->buddies ?? null);
        $guilds = $this->mapGuilds($root->guilds ?? null);
        $top = $this->mapRanking($root->top ?? null);
        $hot = $this->mapRanking($root->hot ?? null);

        return new User(
            Xml::attrInt($root, 'id') ?? 0,
            Xml::attrString($root, 'name') ?? '',
            Xml::childStringValue($root, 'firstname'),
            Xml::childStringValue($root, 'lastname'),
            Xml::childStringValue($root, 'avatarlink'),
            Xml::childIntValue($root, 'yearregistered'),
            Xml::childStringValue($root, 'lastlogin'),
            Xml::childStringValue($root, 'stateorprovince'),
            Xml::childStringValue($root, 'country'),
            Xml::childStringValue($root, 'webaddress'),
            Xml::childStringValue($root, 'xboxaccount'),
            Xml::childStringValue($root, 'wiiaccount'),
            Xml::childStringValue($root, 'psnaccount'),
            Xml::childStringValue($root, 'battlenetaccount'),
            Xml::childStringValue($root, 'steamaccount'),
            Xml::childIntValue($root, 'marketrating'),
            Xml::childIntValue($root, 'traderating'),
            $buddies,
            $guilds,
            $top,
            $hot,
        );
    }

    private function mapBuddies(?\SimpleXMLElement $node): ?UserBuddies
    {
        if (!$node instanceof \SimpleXMLElement) {
            return null;
        }

        $buddies = [];
        foreach (Xml::xpath($node, 'buddy') as $buddyNode) {
            $buddies[] = new UserBuddy(
                Xml::attrInt($buddyNode, 'id') ?? 0,
                Xml::attrString($buddyNode, 'name') ?? '',
            );
        }

        return new UserBuddies(
            Xml::attrInt($node, 'total') ?? 0,
            Xml::attrInt($node, 'page') ?? 0,
            $buddies,
        );
    }

    private function mapGuilds(?\SimpleXMLElement $node): ?UserGuilds
    {
        if (!$node instanceof \SimpleXMLElement) {
            return null;
        }

        $guilds = [];
        foreach (Xml::xpath($node, 'guild') as $guildNode) {
            $guilds[] = new UserGuild(
                Xml::attrInt($guildNode, 'id') ?? 0,
                Xml::attrString($guildNode, 'name') ?? '',
            );
        }

        return new UserGuilds(
            Xml::attrInt($node, 'total') ?? 0,
            Xml::attrInt($node, 'page') ?? 0,
            $guilds,
        );
    }

    private function mapRanking(?\SimpleXMLElement $node): ?UserRanking
    {
        if (!$node instanceof \SimpleXMLElement) {
            return null;
        }

        $items = [];
        foreach (Xml::xpath($node, 'item') as $itemNode) {
            $items[] = new UserRankedItem(
                Xml::attrInt($itemNode, 'rank') ?? 0,
                Xml::attrString($itemNode, 'type') ?? '',
                Xml::attrInt($itemNode, 'id') ?? 0,
                Xml::attrString($itemNode, 'name') ?? '',
            );
        }

        return new UserRanking(
            Xml::attrString($node, 'domain') ?? '',
            $items,
        );
    }
}
