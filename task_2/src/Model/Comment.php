<?php

declare(strict_types=1);

namespace TestAssignment\Model;

final readonly class Comment
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $text
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
