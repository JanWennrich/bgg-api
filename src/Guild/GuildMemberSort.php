<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Guild;

enum GuildMemberSort: string
{
    case Username = 'username';
    case Date = 'date';
}
