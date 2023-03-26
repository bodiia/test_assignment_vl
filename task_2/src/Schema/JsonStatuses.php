<?php

declare(strict_types=1);

namespace TestAssignment\Schema;

enum JsonStatuses: string
{
    case SUCCESS = 'success';
    case FAILED = 'failed';
}
