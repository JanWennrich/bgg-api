<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Test\Console;

use DateTimeImmutable;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Psr7\HttpFactory;
use JanWennrich\BoardGameGeekApi\Client;
use JanWennrich\BoardGameGeekApi\Collection\Collection;
use JanWennrich\BoardGameGeekApi\Family\FamilyType;
use JanWennrich\BoardGameGeekApi\Forum\Forum;
use JanWennrich\BoardGameGeekApi\ForumList\ForumList;
use JanWennrich\BoardGameGeekApi\ForumList\ForumListType;
use JanWennrich\BoardGameGeekApi\Guild\Guild;
use JanWennrich\BoardGameGeekApi\Hot\HotItemType;
use JanWennrich\BoardGameGeekApi\ItemType;
use JanWennrich\BoardGameGeekApi\Plays\Plays;
use JanWennrich\BoardGameGeekApi\Query\CollectionQuery;
use JanWennrich\BoardGameGeekApi\Query\FamilyQuery;
use JanWennrich\BoardGameGeekApi\Query\GuildQuery;
use JanWennrich\BoardGameGeekApi\Query\PlaysQuery;
use JanWennrich\BoardGameGeekApi\Query\SearchQuery;
use JanWennrich\BoardGameGeekApi\Query\ThingQuery;
use JanWennrich\BoardGameGeekApi\Query\ThreadQuery;
use JanWennrich\BoardGameGeekApi\Query\UsersQuery;
use JanWennrich\BoardGameGeekApi\Search\Search;
use JanWennrich\BoardGameGeekApi\Search\SearchType;
use JanWennrich\BoardGameGeekApi\Test\Support\RecordingClient;
use JanWennrich\BoardGameGeekApi\Thing\Thing;
use JanWennrich\BoardGameGeekApi\Thing\ThingType;
use JanWennrich\BoardGameGeekApi\Thread\Thread;
use JanWennrich\BoardGameGeekApi\User\User;
use JanWennrich\BoardGameGeekApi\User\UserDomain;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use Throwable;

#[AsCommand(
    name: 'record-fixtures',
    description: 'Record XML fixtures using the real API via the Client.',
)]
final class RecordFixturesCommand extends SingleCommandApplication
{
    private const THING_ID = 418059;

    private const SECOND_THING_ID = 420033;

    private const FAMILY_ID = 19298;

    private const GUILD_ID = 1303;

    private const FORUM_ID = 1154020;

    private const THREAD_ID = 2860139;

    private const USER = 'Klabauterjan';

    private const SEARCH_QUERY = 'SETI';

    private const PLAYS_ITEM_ID = 418059;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputDir = __DIR__ . '/../../fixtures';

        $authToken = $this->nonEmptyStringOrNull(getenv('BGG_AUTH_TOKEN'));
        $username = $this->nonEmptyStringOrNull(getenv('BGG_USERNAME'));
        $password = $this->nonEmptyStringOrNull(getenv('BGG_PASSWORD'));
        if ($authToken === null || $username === null || $password === null) {
            throw new \InvalidArgumentException('BGG_AUTH_TOKEN, BGG_USERNAME, and BGG_PASSWORD must be set.');
        }

        $httpFactory = new HttpFactory();
        $guzzle = new GuzzleClient(['cookies' => new CookieJar()]);
        $recordingClient = new RecordingClient($guzzle, $outputDir);

        $client = new Client(
            psr18Client: $recordingClient,
            requestFactory: $httpFactory,
            streamFactory: $httpFactory,
        );

        $client->setApiToken($authToken);
        $client->login($username, $password);

        $cases = [
            'thing' => [
                fn(): ?Thing => $client->getThing(self::THING_ID),
                fn(): ?Thing => $client->getThing(self::THING_ID, new ThingQuery(withStats: true)),
                fn(): ?Thing => $client->getThing(self::THING_ID, new ThingQuery(withVersions: true)),
                fn(): ?Thing => $client->getThing(self::THING_ID, new ThingQuery(withVideos: true)),
                fn(): ?Thing => $client->getThing(self::THING_ID, new ThingQuery(withMarketplaceData: true)),
                fn(): ?Thing => $client->getThing(self::THING_ID, new ThingQuery(withComments: true, page: 1, pageSize: 10)),
                fn(): ?Thing => $client->getThing(self::THING_ID, new ThingQuery(withRatingComments: true, page: 1, pageSize: 10)),
                fn(): ?Thing => $client->getThing(self::THING_ID, new ThingQuery(
                    withTypes: [ThingType::BoardGame],
                    withVersions: true,
                    withStats: true,
                )),
                fn(): array => $client->getThings([self::THING_ID, self::SECOND_THING_ID], new ThingQuery(withStats: true)),
            ],
            'search' => [
                fn(): Search => $client->search(self::SEARCH_QUERY),
                fn(): Search => $client->search(self::SEARCH_QUERY, new SearchQuery(onlyExact: true)),
                fn(): Search => $client->search(self::SEARCH_QUERY, new SearchQuery(onlyTypes: [SearchType::BoardGame])),
                fn(): Search => $client->search(self::SEARCH_QUERY, new SearchQuery(onlyTypes: [SearchType::BoardGame], onlyExact: true)),
            ],
            'hot' => [
                $client->getHotItems(...),
                fn(): array => $client->getHotItems(HotItemType::BoardGame),
                fn(): array => $client->getHotItems(HotItemType::Rpg),
            ],
            'collection' => [
                fn(): Collection => $client->getCollection(self::USER),
                fn(): Collection => $client->getCollection(self::USER, new CollectionQuery(withStats: true)),
                fn(): Collection => $client->getCollection(self::USER, new CollectionQuery(onlyBrief: true)),
                fn(): Collection => $client->getCollection(self::USER, new CollectionQuery(withVersions: true)),
                fn(): Collection => $client->getCollection(self::USER, new CollectionQuery(showPrivate: true)),
            ],
            'user' => [
                fn(): ?User => $client->getUser(self::USER),
                fn(): ?User => $client->getUser(self::USER, new UsersQuery(withBuddies: true)),
                fn(): ?User => $client->getUser(self::USER, new UsersQuery(withGuilds: true)),
                fn(): ?User => $client->getUser(self::USER, new UsersQuery(withHot: true, domain: UserDomain::BoardGame)),
                fn(): ?User => $client->getUser(self::USER, new UsersQuery(withTop: true, domain: UserDomain::BoardGame)),
                fn(): ?User => $client->getUser(self::USER, new UsersQuery(withHot: true, withTop: true, domain: UserDomain::BoardGame)),
            ],
            'forumlist' => [
                fn(): ForumList => $client->getForumList(self::THING_ID, ForumListType::Thing),
            ],
            'forum' => [
                fn(): Forum => $client->getForum(self::FORUM_ID, 1),
                fn(): Forum => $client->getForum(self::FORUM_ID, 2),
            ],
            'thread' => [
                fn(): Thread => $client->getThread(self::THREAD_ID),
                fn(): Thread => $client->getThread(self::THREAD_ID, new ThreadQuery(count: 10)),
            ],
            'guild' => [
                fn(): ?Guild => $client->getGuild(self::GUILD_ID),
                fn(): ?Guild => $client->getGuild(self::GUILD_ID, new GuildQuery(withMembers: true, page: 1)),
            ],
            'family' => [
                fn(): ?array => $client->getFamily(self::FAMILY_ID),
                fn(): ?array => $client->getFamily(self::FAMILY_ID, new FamilyQuery(withTypes: [FamilyType::BoardGameFamily])),
            ],
            'plays' => [
                fn(): Plays => $client->getPlaysForUser(self::USER),
                fn(): Plays => $client->getPlaysForUser(self::USER, new PlaysQuery(page: 1)),
                fn(): Plays => $client->getPlaysForUser(self::USER, new PlaysQuery(
                    minDate: new DateTimeImmutable('2025-01-01 00:00:00'),
                    maxDate: new DateTimeImmutable('2025-12-31 23:59:59'),
                )),
            ],
            'plays-item' => [
                fn(): Plays => $client->getPlaysForItem(self::PLAYS_ITEM_ID, ItemType::Thing),
            ],
        ];

        foreach ($cases as $endpoint => $callbacks) {
            foreach ($callbacks as $i => $callback) {
                $label = "{$endpoint} #" . ($i + 1);
                try {
                    $callback();
                    $output->writeln("Recorded {$label}");
                } catch (Throwable $throwable) {
                    $output->writeln("Failed {$label}: {$throwable->getMessage()}");
                }
            }
        }

        return self::SUCCESS;
    }

    /**
     * @return non-empty-string|null
     */
    private function nonEmptyStringOrNull(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        return $trimmed;
    }
}
