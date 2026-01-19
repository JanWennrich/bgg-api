<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi;

enum HotItemType: string
{
    case BoardGame = 'boardgame';
    case Rpg = 'rpg';
    case VideoGame = 'videogame';
    case BoardGamePerson = 'boardgameperson';
    case RpgPerson = 'rpgperson';
    case BoardGameCompany = 'boardgamecompany';
    case RpgCompany = 'rpgcompany';
    case VideoGameCompany = 'videogamecompany';
}
