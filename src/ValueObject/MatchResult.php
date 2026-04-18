<?php

declare(strict_types=1);

namespace PolishProfanityFilter\ValueObject;

final class MatchResult
{
    public function __construct(
        public readonly string $word,
        public readonly int $start,
        public readonly int $end,
    ) {}
}
