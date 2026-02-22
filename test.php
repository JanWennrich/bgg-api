<?php

declare(strict_types=1);

use JanWennrich\BoardGameGeekApi\Client;

require __DIR__ . '/vendor/autoload.php';


$client = Client::autocreate();
//$client->login('Klabauterjan', getenv('BGG_PASSWORD'));
$client->setApiToken('1c0b15ba-8118-4c02-97c5-32ce890f2a84');

$result = $client->getThing(418059, new \JanWennrich\BoardGameGeekApi\Query\ThingQuery(
    withVersions: true,
    withVideos: true,
    withStats: true,
    withMarketplaceData: true,
    withComments: true
));

var_dump($result);
