<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi;

enum FamilyType: string
{
    case BoardGameFamily = 'boardgamefamily';
    case Rpg = 'rpg';
    case RpgPeriodical = 'rpgperiodical';
}
