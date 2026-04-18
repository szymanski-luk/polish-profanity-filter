<?php

declare(strict_types=1);

namespace PolishProfanityFilter\SearchPattern;

interface SearchPatternInterface
{
    public function buildPattern(string $word): string;
}
