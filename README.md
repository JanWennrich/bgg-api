# jan-wennrich/bgg-api
![Logo](/README-logo.webp)

[![Packagist Version](https://img.shields.io/packagist/v/jan-wennrich/bgg-api)](https://packagist.org/packages/jan-wennrich/bgg-api)
[![Packagist Downloads](https://img.shields.io/packagist/dt/jan-wennrich/bgg-api)](https://packagist.org/packages/jan-wennrich/bgg-api)
[![PHP Version Require](https://img.shields.io/packagist/php-v/jan-wennrich/bgg-api)](https://packagist.org/packages/jan-wennrich/bgg-api)
[![License](https://img.shields.io/packagist/l/jan-wennrich/bgg-api)](https://github.com/JanWennrich/bgg-api/blob/main/LICENSE)
[![CI Status](https://github.com/JanWennrich/bgg-api/actions/workflows/test.yml/badge.svg)](https://github.com/JanWennrich/bgg-api/actions)

A modern **PHP API client library** for [BoardGameGeek.com](https://boardgamegeek.com) using the **XML API2**.

## Installation

Require the package with Composer:

```bash
composer require jan-wennrich/bgg-api
```

## Usage

### Creating the client

```php
use JanWennrich\BoardGameGeekApi\Client;

$client = new Client(?LoggerInterface $logger = null, ?GuzzleClient $guzzleClient = null);
```

You can optionally pass:
- a `Psr\Log\LoggerInterface` (optional; defaults to `NullLogger`)
- a `custom GuzzleHttp\Client` (optional)

### Authentication

Recently the BoardGameGeek API requires authentication to access it.

You must authenticate via an BoardGameGeek [API token](https://boardgamegeek.com/using_the_xml_api) (recommended) or a BoardGameGeek username & password.

#### API Token

An API Token can be obtained from BoardGameGeek: https://boardgamegeek.com/using_the_xml_api

```php
$client->setAuthorization($apiToken);
```

#### Username & Password

> [!IMPORTANT]
> Authenticating via username & passwords limits the access to resources of the given username.  
> So you can access the user's collection or plays for example but no general resources like boardgames.

```php
$client->login($username, $password);
```

The login is stored during runtime, so you have to log in again after the program terminates.

### Endpoints

#### Things (BoardGames)

To get a single `Thing` you can use the following method:

```php
$client->getThing(string $id, bool $stats = false, bool $versions = false): ?Thing
```

To get multiple `Things` at once, use the following method:

```php
$client->getThings(array $ids, bool $stats = false, bool $versions = false): Thing[]
```

A single `Thing` provides the following API:
- Basic info:
    - `getId()`, `getType()`, `isType($type)`
    - `getName()`, `getDescription()`
    - `getImage()`, `getThumbnail()`
    - `getYearPublished()`
    - `getMinPlayers()`, `getMaxPlayers()`
    - `getPlayingTime()`, `getMinPlayTime()`, `getMaxPlayTime()`
    - `getMinAge()`
- Links & related entities:
    - `getBoardgameCategories()`, `getBoardgameMechanics()`
    - `getBoardgameDesigners()`, `getBoardgameArtists()`, `getBoardgamePublishers()`
    - `getBoardgameExpansions()`
    - `getBoardgameVersions()`
    - `getLinks()` (raw link objects)
    - `getAlternateNames()`
    - `getBoardgameBasegame()`
- Stats (if `stats=true` when requesting the `Thing`):
    - `getRatingAverage()`
    - `getWeightAverage()`
    - `getRank()`
    - `getLanguageDependenceLevel()`

_Example: Get the thumbnails of "Ark Nova" (ID `342942`) and "Gloomhaven" (ID `174430`)_
```php
$things = $client->getThings([342942, 174430]);

foreach ($things as $thing) {
    echo $thing->getThumbnail() . PHP_EOL; 
}
```

#### Search

To search for something you can use the following method:

```php
$client->search(string $query, bool $exact = false, string $type = Type::BOARDGAME): Search\Query
```

Each item in the `Seach\Query` is a `Search\Result` with:
- `getId()`, `getType()`, `isType($type)`
- `getName()`
- `getYearPublished()`

_Example: Search for the board game "Terraforming Mars" and list the names of search results_
```php
use JanWennrich\BoardGameGeekApi\Type;

$query = $client->search('Terraforming Mars', exact: true, type: Type::BOARDGAME);

echo count($query) . PHP_EOL;

$first = $query[0];
if ($first) {
    echo $first->getName() . PHP_EOL;
}
```

#### Hot items

To get hot items you can use this method:

```php
$client->getHotItems(string $type = Type::BOARDGAME): HotItem[]
```

A `HotItem` provides these getters:
- `getId()`
- `getRank()`
- `getName()`
- `getYearPublished()`
- `getThumbnail()`

_Example: Get hot items and list their ranks and names_
```php
$hot = $client->getHotItems();
foreach ($hot as $item) {
    echo $item->getRank() . ': ' . $item->getName() . PHP_EOL;
}
```

#### User

To get a use you can use this method:

```php
$client->getUser(string $name): ?User
```

A `User` provides the following getters:
- `getId()`, `getLogin()`, `getName()`
- `getFirstName()`, `getLastName()`
- `getAvatar()`, `getCountry()`
- `getYearRegistered()`

_Example: Get the avatar of user "Klabauterjan"_
```php
$user = $client->getUser('Klabauterjan');
echo $user->getAvatar();
```

#### Collection

To get a collection, you can use this method:

```php
$client->getCollection(array $parameters): Collection
```

All parameters as specified by the BoardGameGeek API are supported: https://boardgamegeek.com/wiki/page/BGG_XML_API2#toc13

Each entry of a Collection is a `Collection\Item` with these getters:
- object info: `getObjectType()`, `getObjectId()`, `getSubtype()`, `getCollId()`
- metadata: `getName()`, `getYearPublished()`, `getImage()`, `getThumbnail()`
- stats: `getNumPlays()`, `getRatingAverage()`
- players/time: `getMinPlayers()`, `getMaxPlayers()`, `getPlayingTime()`, `getMinPlayTime()`, `getMaxPlayTime()`
- status: `getStatus(): Collection\ItemStatus`

A `Collection\ItemStatus` provides this API:
- flags: `isOwn()`, `isPrevOwned()`, `isForTrade()`, `isWant()`, `isWantToPlay()`, `isWantToBuy()`, `isWishlist()`, `isPreordered()`
- wishlist info: `getWishlistPriority()`
- timestamps: `getLastModified()`

_Example: Get all boardgames owned by user "Klabauterjan" and list them with their name and publish date_
```php
$collection = $client->getCollection([
    'username' => 'Klabauterjan',
    'owned' => 1,
    'stats' => 1
]);

echo $collection->count() . PHP_EOL;

foreach ($collection as $item) {
    echo $item->getName() . PHP_EOL;
    echo $item->getYearPublished() . PHP_EOL;
}
```

#### Plays

To get plays, you can use this method:

```php
$client->getPlays(array $parameters): Play[]
```

All parameters as specified by the BoardGameGeek API are supported: https://boardgamegeek.com/wiki/page/BGG_XML_API2#toc12

A `Play` object provides these getters:
- core: `getId()`, `getDate()`, `getQuantity()`, `getLength()`
- flags: `isIncomplete()`, `isNoWinStats()`
- location/comments: `getLocation()`, `getComments()`
- item info: `getObjectType()`, `getObjectId()`, `getObjectName()`, `getSubtypes()`
- players: `getPlayers(): Player[]`

A `Player` object provides these getters:
- identity: `getUsername()`, `getUserid()`, `getName()`
- game info: `getStartPosition()`, `getColor()`, `getScore()`, `getRating()`
- flags: `isNew()`, `isWin()`

_Example: Get plays of user "Klabauterjan" and list their date, name of the game and the names of players_
```php
$plays = $client->getPlays(['username' => 'Klabauterjan']);

foreach ($plays as $play) {
    echo $play->getDate() . ' - ' . $play->getObjectName() . PHP_EOL;

    foreach ($play->getPlayers() as $player) {
        echo '  - ' . ($player->getName() ?: $player->getUsername()) . PHP_EOL;
    }
}
```

### Reliability / Retries

The BoardGameGeek API does not immediately provide results to most requests.  
Instead it returns a `HTTP 202` response and will return the actual data with a later request.

To simplify this, the client has built-in retry behavior when fetching data:
- Retries up to **3 times**
- Waits **5 seconds** between attempts
- Automatically retries when BGG responds with **HTTP 202** (queued response)
- Retries on transport errors and **5xx** responses 
- Does **not** keep retrying on typical **4xx** client errors (e.g. missing authentication)

If all attempts fail, an `Exception` is thrown.

## Testing & Development

To develop/test this library, do the following:

1. Clone the repository
2. Install dependencies (`composer install`)

Tests can be executed by calling:

```bash
composer test
```

The library uses the following tools to ensure functionality & stability:
- [PHPUnit](https://github.com/sebastianbergmann/phpunit) for testing
- [PHPStan](https://github.com/phpstan/phpstan) for static analysis
- [PHP CS Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) to enforce code style
- [Rector](https://github.com/rectorphp/rector) for automated refactorings
- [Composer Dependency Analyser](https://github.com/shipmonk-rnd/composer-dependency-analyser) for finding unused or unspecified requirements
- GitHub Actions CI for automatic testing

## Credits

This library is a fork of [castro732/bggxmlapi2](https://github.com/castro732/bggxmlapi2) which is a fork of [nataniel/bggxmlapi2](https://github.com/nataniel/bggxmlapi2).  
Thanks to the original authors for the foundation.

## License

MIT License 

See [`LICENSE`](/LICENSE) for details.