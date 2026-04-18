<?php

declare(strict_types=1);

namespace PolishProfanityFilter\Mask;

use InvalidArgumentException;

final class AsteriskMasker implements MaskerInterface
{
    public function __construct(
        private readonly int $visiblePrefix = 1,
        private readonly int $visibleSuffix = 1,
    ) {
        if ($visiblePrefix < 0) {
            throw new InvalidArgumentException('visiblePrefix cannot be negative');
        }

        if ($visibleSuffix < 0) {
            throw new InvalidArgumentException('visibleSuffix cannot be negative');
        }
    }

    public function mask(string $word): string
    {
        $length = mb_strlen($word);

        if ($this->visiblePrefix === 0 && $this->visibleSuffix === 0) {
            return str_repeat('*', $length);
        }

        $visible = $this->visiblePrefix + $this->visibleSuffix;

        if ($length <= $visible) {
            return str_repeat('*', $length);
        }

        $prefix = mb_substr($word, 0, $this->visiblePrefix);
        $suffix = mb_substr($word, -$this->visibleSuffix);

        return $prefix
            . str_repeat('*', $length - $visible)
            . $suffix;
    }
}
