<?php

declare(strict_types=1);

namespace JanWennrich\BoardGameGeekApi\Query;

use JanWennrich\BoardGameGeekApi\FamilyType;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

final readonly class FamilyQuery
{
    /**
     * @param non-empty-array<FamilyType>|null $withTypes Specify that, regardless of the type of family asked for by id, the results are filtered by the {@see FamilyType}(s) specified. Multiple {@see FamilyType}s can be specified.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        public ?array $withTypes = null,
    ) {
        if (is_array($this->withTypes)) {
            Assert::notEmpty($this->withTypes);
            Assert::allIsInstanceOf($this->withTypes, FamilyType::class);
        }
    }
}
