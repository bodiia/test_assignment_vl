<?php

declare(strict_types=1);

namespace TestAssignment\DTO;

abstract class BaseDto
{
    public static function create(...$params): static
    {
        return new static(...$params);
    }
}
