<?php

declare(strict_types=1);

namespace PolishProfanityFilter\SearchPattern;

class DefaultSearchPattern implements SearchPatternInterface
{
    public function buildPattern(string $word): string
    {
        return '/(?<![\p{L}\p{N}_])' . preg_quote($word, '/') . '(?![\p{L}\p{N}_])/iu';
    }
}
