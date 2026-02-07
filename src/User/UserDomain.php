<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\User;

enum UserDomain: string
{
    case BoardGame = 'boardgame';
    case Rpg = 'rpg';
    case VideoGame = 'videogame';
}
