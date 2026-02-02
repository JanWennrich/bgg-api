<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi;

/**
 * @internal
 */
enum BggApiEndpoint: string
{
    case Thing = 'thing';
    case Family = 'family';
    case ForumList = 'forumlist';
    case Forum = 'forum';
    case Thread = 'thread';
    case User = 'user';
    case Guild = 'guild';
    case Plays = 'plays';
    case Collection = 'collection';
    case Hot = 'hot';
    case Search = 'search';
}
