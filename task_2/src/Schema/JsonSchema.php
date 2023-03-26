<?php

declare(strict_types=1);

namespace TestAssignment\Schema;

final class JsonSchema
{
    private readonly JsonStatuses $status;

    private readonly array $data;

    public function __construct(array $json)
    {
        $this->status = JsonStatuses::from($json['status']);
        $this->data = $json['data'];
    }

    public function getStatus(): JsonStatuses
    {
        return $this->status;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
