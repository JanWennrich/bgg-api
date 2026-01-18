<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi;

enum UserDomain: string
{
    case BoardGame = 'boardgame';
    case Rpg = 'rpg';
    case VideoGame = 'videogame';
}
