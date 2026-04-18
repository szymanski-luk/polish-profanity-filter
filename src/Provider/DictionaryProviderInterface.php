<?php

declare(strict_types=1);

namespace PolishProfanityFilter\Provider;

interface DictionaryProviderInterface
{
    /** @return string[] */
    public function getDictionary(): array;
}
