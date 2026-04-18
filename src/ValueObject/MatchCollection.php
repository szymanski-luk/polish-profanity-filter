<?php

declare(strict_types=1);

namespace PolishProfanityFilter\ValueObject;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, MatchResult>
 */
final class MatchCollection implements Countable, IteratorAggregate
{
    /** @var MatchResult[] */
    private array $matches;

    /**
     * @param MatchResult[] $matches
     */
    public function __construct(array $matches)
    {
        $this->matches = array_values($matches);
    }

    public function isEmpty(): bool
    {
        return $this->matches === [];
    }

    public function count(): int
    {
        return count($this->matches);
    }

    /**
     * @return MatchResult[]
     */
    public function all(): array
    {
        return $this->matches;
    }

    public function first(): ?MatchResult
    {
        return $this->matches[0] ?? null;
    }

    public function last(): ?MatchResult
    {
        if ($this->matches === []) {
            return null;
        }

        return $this->matches[array_key_last($this->matches)];
    }

    public function containsWord(string $word, bool $caseInsensitive = true): bool
    {
        if ($caseInsensitive) {
            $word = mb_strtolower($word);
        }

        foreach ($this->matches as $match) {
            $candidate = $caseInsensitive
                ? mb_strtolower($match->word)
                : $match->word;

            if ($candidate === $word) {
                return true;
            }
        }

        return false;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->matches);
    }
}
