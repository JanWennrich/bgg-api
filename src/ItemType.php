<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi;

enum ItemType: string
{
    case Thing = 'thing';
    case Family = 'family';
}
