<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi;

enum PlayType: string
{
    case BoardGame = 'boardgame';
    case BoardGameExpansion = 'boardgameexpansion';
    case BoardGameAccessory = 'boardgameaccessory';
    case BoardGameIntegration = 'boardgameintegration';
    case BoardGameCompilation = 'boardgamecompilation';
    case BoardGameImplementation = 'boardgameimplementation';
    case Rpg = 'rpg';
    case RpgItem = 'rpgitem';
    case VideoGame = 'videogame';
}
