<?php

declare(strict_types=1);

namespace TestAssignment\Client;

use TestAssignment\Model\Comment;

interface CommentsClientInterface
{
    /**
     * @return Comment[]
     */
    public function get(array $query = []): array;

    public function create(array $fields): Comment;

    public function update(int $id, array $fields): Comment;
}
