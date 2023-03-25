<?php

declare(strict_types=1);

namespace TestAssignment\Client;

use TestAssignment\DTO\CommentDto;

interface CommentsClientInterface
{
    /**
     * @return CommentDto[]
     */
    public function get(array $query = []): array;

    public function create(CommentDto $dto): CommentDto;

    public function update(CommentDto $dto): CommentDto;
}
