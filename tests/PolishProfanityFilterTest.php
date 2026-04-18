<?php

declare(strict_types=1);

namespace PolishProfanityFilter\Tests;

use PHPUnit\Framework\TestCase;
use PolishProfanityFilter\Mask\AsteriskMasker;
use PolishProfanityFilter\PolishProfanityFilter;
use PolishProfanityFilter\Tests\Double\FakeDictionaryProvider;

class PolishProfanityFilterTest extends TestCase
{
    public function testItDetectsProfanity(): void
    {
        $filter = new PolishProfanityFilter(
            defaultDictionaryProvider: new FakeDictionaryProvider(['profanity'])
        );

        self::assertTrue($filter->containsProfanity('This text contains profanity.'));
    }

    public function testItDoesNotDetectProfanityWhenTextIsClean(): void
    {
        $filter = new PolishProfanityFilter(
            defaultDictionaryProvider: new FakeDictionaryProvider(['profanity'])
        );

        self::assertFalse($filter->containsProfanity('This is normal text.'));
    }

    public function testItDetectsProfanityCaseInsensitively(): void
    {
        $filter = new PolishProfanityFilter(
            defaultDictionaryProvider: new FakeDictionaryProvider(['profanity'])
        );

        self::assertTrue($filter->containsProfanity('There is a PrOfAnItY somewhere around here.'));
    }

    public function testFindProfanitiesReturnsEmptyCollectionWhenNothingIsFound(): void
    {
        $filter = new PolishProfanityFilter(
            defaultDictionaryProvider: new FakeDictionaryProvider(['profanity'])
        );

        $matches = $filter->findProfanities('This is a normal text.');

        self::assertTrue($matches->isEmpty());
        self::assertSame(0, $matches->count());
    }

    public function testFindProfanitiesFindsSingleWord(): void
    {
        $filter = new PolishProfanityFilter(
            defaultDictionaryProvider: new FakeDictionaryProvider(['profanity'])
        );

        $matches = $filter->findProfanities('This text contains profanity.');

        self::assertFalse($matches->isEmpty());
        self::assertSame(1, $matches->count());

        $first = $matches->first();

        self::assertNotNull($first);
        self::assertSame('profanity', $first->word);
        self::assertSame(19, $first->start);
        self::assertSame(28, $first->end);
    }

    public function testFindProfanitiesFindsMultipleWords(): void
    {
        $filter = new PolishProfanityFilter(
            defaultDictionaryProvider: new FakeDictionaryProvider(['profanity', 'swear'])
        );

        $matches = $filter->findProfanities('Profanity... and swear!');

        self::assertSame(2, $matches->count());

        $all = $matches->all();

        self::assertSame('Profanity', $all[0]->word);
        self::assertSame(0, $all[0]->start);
        self::assertSame(9, $all[0]->end);

        self::assertSame('swear', $all[1]->word);
        self::assertSame(17, $all[1]->start);
        self::assertSame(22, $all[1]->end);
    }

    public function testFindProfanitiesIsCaseInsensitive(): void
    {
        $filter = new PolishProfanityFilter(
            defaultDictionaryProvider: new FakeDictionaryProvider(['profanity'])
        );

        $matches = $filter->findProfanities('This text contains PrOfAnIty.');

        self::assertSame(1, $matches->count());

        $first = $matches->first();

        self::assertNotNull($first);
        self::assertSame('PrOfAnIty', $first->word);
        self::assertSame(19, $first->start);
        self::assertSame(28, $first->end);
    }

    public function testMaskProfanitiesMasksSingleWord(): void
    {
        $filter = new PolishProfanityFilter(
            defaultDictionaryProvider: new FakeDictionaryProvider(['kurwa']),
            masker: new AsteriskMasker()
        );

        $result = $filter->maskProfanities('To jest kurwa test.');

        self::assertSame('To jest k***a test.', $result);
    }

    public function testMaskProfanitiesMasksMultipleWords(): void
    {
        $filter = new PolishProfanityFilter(
            defaultDictionaryProvider: new FakeDictionaryProvider(['profanity', 'swear']),
            masker: new AsteriskMasker()
        );

        $result = $filter->maskProfanities('Profanity and swear.');

        self::assertSame('P*******y and s***r.', $result);
    }

    public function testMaskProfanitiesLeavesCleanTextUnchanged(): void
    {
        $filter = new PolishProfanityFilter(
            defaultDictionaryProvider: new FakeDictionaryProvider(['profanity']),
            masker: new AsteriskMasker()
        );

        $result = $filter->maskProfanities('This is a normal text.');

        self::assertSame('This is a normal text.', $result);
    }

    public function testMaskProfanitiesIsCaseInsensitive(): void
    {
        $filter = new PolishProfanityFilter(
            defaultDictionaryProvider: new FakeDictionaryProvider(['profanity']),
            masker: new AsteriskMasker()
        );

        $result = $filter->maskProfanities('There is a PrOfAnItY somewhere around here.');

        self::assertSame('There is a P*******Y somewhere around here.', $result);
    }

    public function testItDetectsProfanityFromAdditionalDictionary(): void
    {
        $filter = new PolishProfanityFilter(
            defaultDictionaryProvider: new FakeDictionaryProvider(['profanity']),
            additionalDictionaries: [new FakeDictionaryProvider(['swear'])]
        );

        self::assertTrue($filter->containsProfanity('This text contains swear.'));
    }
}
