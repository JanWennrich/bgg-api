<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi;

enum SearchType: string
{
    case RpgItem = 'rpgitem';
    case VideoGame = 'videogame';
    case BoardGame = 'boardgame';
    case BoardGameAccessory = 'boardgameaccessory';
    case BoardGameExpansion = 'boardgameexpansion';
    case BoardGameDesigner = 'boardgamedesigner';
}
