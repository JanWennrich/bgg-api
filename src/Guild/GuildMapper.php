<?php

namespace JanWennrich\BoardGameGeekApi\Guild;

use JanWennrich\BoardGameGeekApi\Xml;

final class GuildMapper
{
    public function fromXml(\SimpleXMLElement $root): Guild
    {
        $locationNode = $root->location ?? null;
        $guildLocation = new GuildLocation(
            Xml::childText($locationNode->addr1 ?? null) ?? '',
            Xml::childText($locationNode->addr2 ?? null) ?? '',
            Xml::childText($locationNode->city ?? null) ?? '',
            Xml::childText($locationNode->stateorprovince ?? null) ?? '',
            Xml::childText($locationNode->postalcode ?? null) ?? '',
            Xml::childText($locationNode->country ?? null) ?? '',
        );

        $membersNode = $root->members ?? null;
        $members = null;
        if ($membersNode !== null) {
            $memberItems = [];
            foreach (Xml::xpath($membersNode, 'member') as $memberNode) {
                $memberItems[] = new GuildMember(
                    Xml::attrString($memberNode, 'name') ?? '',
                    Xml::attrString($memberNode, 'date') ?? '',
                );
            }

            $members = new GuildMembers(
                Xml::attrInt($membersNode, 'count') ?? 0,
                Xml::attrInt($membersNode, 'page') ?? 0,
                $memberItems,
            );
        }

        return new Guild(
            Xml::attrInt($root, 'id') ?? 0,
            Xml::attrString($root, 'name') ?? '',
            Xml::attrString($root, 'created') ?? '',
            Xml::childText($root->category ?? null) ?? '',
            Xml::childText($root->website ?? null) ?? '',
            Xml::childText($root->manager ?? null) ?? '',
            Xml::childText($root->description ?? null) ?? '',
            $guildLocation,
            $members,
        );
    }
}
