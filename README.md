# jan-wennrich/bgg-api

PHP API Client Library for the BoardGameGeek.com API.

_(Only the [BGG XML API2](https://boardgamegeek.com/wiki/page/BGG_XML_API2) is currently supported)_

## Installation
```
composer require jan-wennrich/bgg-api
```

## Usage

```php
// initialize client
$client = new \JanWennrich\BoardGameGeekApi\Client();

// download information about "Dixit"
// https://boardgamegeek.com/boardgame/39856/dixit
$thing = $client->getThing(39856, true);

var_dump($thing->getName());
var_dump($thing->getYearPublished());
var_dump($thing->getBoardgameCategories());
var_dump($thing->getRatingAverage());
// ...

// download information about user
// https://boardgamegeek.com/user/Nataniel
$user = $client->getUser('nataniel');

var_dump($user->getAvatar());
var_dump($user->getCountry());

// search for a game
$results = $client->search('Domek');
echo count($results);

$things = [];
foreach ($result as $item) {
    var_dump($item->getName());
    $things[] = $client->getThing($item->getId());
}
```

## Credits

This is a fork of [castro732/bggxmlapi2](https://github.com/castro732/bggxmlapi2) which is a fork of [nataniel/bggxmlapi2](https://github.com/nataniel/bggxmlapi2).