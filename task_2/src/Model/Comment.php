<?php

declare(strict_types=1);

namespace TestAssignment\Model;

final class Comment
{
    public function __construct(
        private readonly ?int $id,
        private readonly string $name,
        private readonly string $text
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
