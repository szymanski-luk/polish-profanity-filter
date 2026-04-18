<?php

declare(strict_types=1);

namespace PolishProfanityFilter\Provider;

class DefaultDictionaryProvider implements DictionaryProviderInterface
{
    public function getDictionary(): array
    {
        return require __DIR__ . '/../Resources/dictionary_pl.php';
    }
}
