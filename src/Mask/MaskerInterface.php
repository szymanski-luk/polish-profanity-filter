<?php

declare(strict_types=1);

namespace PolishProfanityFilter\Mask;

interface MaskerInterface
{
    public function mask(string $word): string;
}
