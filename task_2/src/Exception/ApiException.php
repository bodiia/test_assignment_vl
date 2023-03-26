<?php

declare(strict_types=1);

namespace TestAssignment\Exception;

use Psr\Http\Message\ResponseInterface;

class ApiException extends \Exception
{
    public function __construct(string $message, private readonly ResponseInterface $response)
    {
        parent::__construct($message);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
