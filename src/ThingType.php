<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi;

enum ThingType: string
{
    case BoardGame = 'boardgame';
    case BoardGameExpansion = 'boardgameexpansion';
    case BoardGameAccessory = 'boardgameaccessory';
    case VideoGame = 'videogame';
    case RpgItem = 'rpgitem';
    case RpgIssue = 'rpgissue';
}
