<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\ForumList;

enum ForumListType: string
{
    case Thing = 'thing';
    case Family = 'family';
}
