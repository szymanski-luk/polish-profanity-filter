<?php

declare(strict_types=1);

namespace PolishProfanityFilter\Tests\Mask;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PolishProfanityFilter\Mask\AsteriskMasker;

final class AsteriskMaskerTest extends TestCase
{
    public function testItMasksWordLeavingFirstAndLastCharacter(): void
    {
        $masker = new AsteriskMasker();

        self::assertSame('p*******y', $masker->mask('profanity'));
    }

    public function testItMasksShortWordCompletely(): void
    {
        $masker = new AsteriskMasker();

        self::assertSame('**', $masker->mask('ab'));
    }

    public function testItThrowsExceptionForNegativePrefix(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AsteriskMasker(-1, 1);
    }
}
