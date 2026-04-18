<?php

declare(strict_types=1);

namespace PolishProfanityFilter\Tests\Double;

use PolishProfanityFilter\Provider\DictionaryProviderInterface;

final class FakeDictionaryProvider implements DictionaryProviderInterface
{
    /**
     * @param string[] $dictionary
     */
    public function __construct(
        private readonly array $dictionary
    ) {}

    public function getDictionary(): array
    {
        return $this->dictionary;
    }
}
