<?php

declare(strict_types=1);

namespace PolishProfanityFilter\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use PolishProfanityFilter\ValueObject\MatchCollection;
use PolishProfanityFilter\ValueObject\MatchResult;

final class MatchCollectionTest extends TestCase
{
    public function testItIsEmptyWhenConstructedWithNoMatches(): void
    {
        $collection = new MatchCollection([]);

        self::assertTrue($collection->isEmpty());
        self::assertSame(0, $collection->count());
        self::assertSame([], $collection->all());
        self::assertNull($collection->first());
        self::assertNull($collection->last());
    }

    public function testItReturnsFirstAndLastMatch(): void
    {
        $first = new MatchResult('profanity', 2, 7);
        $second = new MatchResult('swear', 10, 14);

        $collection = new MatchCollection([$first, $second]);

        self::assertFalse($collection->isEmpty());
        self::assertSame(2, $collection->count());
        self::assertSame($first, $collection->first());
        self::assertSame($second, $collection->last());
        self::assertSame([$first, $second], $collection->all());
    }

    public function testContainsWordIsCaseInsensitiveByDefault(): void
    {
        $collection = new MatchCollection([
            new MatchResult('profanity', 0, 5),
        ]);

        self::assertTrue($collection->containsWord('profanity'));
        self::assertTrue($collection->containsWord('PROFANITY'));
        self::assertTrue($collection->containsWord('pRoFaNiTy'));
    }

    public function testContainsWordCanBeCaseSensitive(): void
    {
        $collection = new MatchCollection([
            new MatchResult('profanity', 0, 5),
        ]);

        self::assertTrue($collection->containsWord('profanity', false));
        self::assertFalse($collection->containsWord('PROFANITY', false));
        self::assertFalse($collection->containsWord('pRoFaNiTy', false));
    }

    public function testContainsWordReturnsFalseWhenWordDoesNotExist(): void
    {
        $collection = new MatchCollection([
            new MatchResult('profanity', 0, 5),
        ]);

        self::assertFalse($collection->containsWord('swear'));
    }

    public function testItIsIterable(): void
    {
        $matches = [
            new MatchResult('profanity', 0, 5),
            new MatchResult('swear', 10, 14),
        ];

        $collection = new MatchCollection($matches);

        $iterated = [];

        foreach ($collection as $match) {
            $iterated[] = $match;
        }

        self::assertSame($matches, $iterated);
    }
}
