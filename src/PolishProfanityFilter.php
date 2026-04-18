<?php

declare(strict_types=1);

namespace PolishProfanityFilter;

use PolishProfanityFilter\Mask\AsteriskMasker;
use PolishProfanityFilter\Mask\MaskerInterface;
use PolishProfanityFilter\Provider\DefaultDictionaryProvider;
use PolishProfanityFilter\Provider\DictionaryProviderInterface;
use PolishProfanityFilter\SearchPattern\DefaultSearchPattern;
use PolishProfanityFilter\SearchPattern\SearchPatternInterface;
use PolishProfanityFilter\ValueObject\MatchCollection;
use PolishProfanityFilter\ValueObject\MatchResult;

final class PolishProfanityFilter
{
    private SearchPatternInterface $searchPattern;
    /** @var string[] */
    private readonly array $dictionary;
    private readonly MaskerInterface $masker;

    /** @param DictionaryProviderInterface[] $additionalDictionaryProviders */
    public function __construct(
        ?DictionaryProviderInterface $defaultDictionaryProvider = null,
        array $additionalDictionaryProviders = [],
        ?SearchPatternInterface $searchPattern = null,
        ?MaskerInterface $masker = null,
    ) {
        $defaultDictionaryProvider ??= new DefaultDictionaryProvider();
        $this->dictionary = $this->buildDictionary($defaultDictionaryProvider, $additionalDictionaryProviders);
        $this->searchPattern = $searchPattern ?? new DefaultSearchPattern();
        $this->masker = $masker ?? new AsteriskMasker();
    }

    public function containsProfanity(string $text): bool
    {
        foreach ($this->dictionary as $word) {
            $pattern = $this->searchPattern->buildPattern($word);

            if (preg_match($pattern, $text) === 1) {
                return true;
            }
        }

        return false;
    }

    public function findProfanities(string $text): MatchCollection
    {
        $results = [];

        foreach ($this->dictionary as $word) {
            $pattern = $this->searchPattern->buildPattern($word);

            if (preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as [$match, $byteOffset]) {
                    $charOffset = mb_strlen(substr($text, 0, (int) $byteOffset));
                    $charLength = mb_strlen($match);

                    $results[] = new MatchResult($match, $charOffset, $charOffset + $charLength);
                }
            }
        }

        return new MatchCollection($results);
    }

    public function maskProfanities(string $text): string
    {
        foreach ($this->dictionary as $word) {
            $pattern = $this->searchPattern->buildPattern($word);

            $text = preg_replace_callback(
                $pattern,
                fn(array $match): string => $this->masker->mask($match[0]),
                $text
            ) ?? $text;
        }

        return $text;
    }

    /**
     * @param DictionaryProviderInterface[] $additionalDictionaryProviders
     *
     * @return string[]
     */
    private function buildDictionary(
        DictionaryProviderInterface $defaultDictionaryProvider,
        array $additionalDictionaryProviders
    ): array {
        $dictionary = $defaultDictionaryProvider->getDictionary();

        foreach ($additionalDictionaryProviders as $additionalDictionaryProvider) {
            $dictionary = array_merge(
                $dictionary,
                $additionalDictionaryProvider->getDictionary()
            );
        }

        return array_values(array_unique($dictionary));
    }
}
