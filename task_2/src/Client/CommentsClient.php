<?php

declare(strict_types=1);

namespace TestAssignment\Client;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use TestAssignment\Exception\ApiException;
use TestAssignment\Model\Comment;
use TestAssignment\Hydrator\Hydrator;
use TestAssignment\Schema\JsonSchema;
use TestAssignment\Schema\JsonStatuses;

final class CommentsClient implements CommentsClientInterface
{
    private readonly Hydrator $hydrator;

    private readonly ClientInterface $httpClient;

    public function __construct(?ClientInterface $httpClient)
    {
        $config = [
            'base_uri' => 'https://example.com',
        ];

        $this->httpClient = $httpClient ?? new Client($config);
        $this->hydrator = new Hydrator();
    }

    public function get(array $query = []): array
    {
        $options = [
            RequestOptions::QUERY => $query
        ];

        $comments = $this->handleResponse(
            $this->httpClient->get('/comments', $options)
        );

        return $this->hydrator->hydrateObjects(Comment::class, $comments);
    }

    public function create(array $fields): Comment
    {
        $options = [
            RequestOptions::BODY => json_encode($fields),
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
            ],
        ];

        $raw = $this->handleResponse(
            $this->httpClient->post('/comment', $options)
        );

        /** @var Comment $comment */
        $comment = $this->hydrator->hydrateObject(Comment::class, $raw);

        return $comment;
    }

    public function update(int $id, array $fields): Comment
    {
        $options = [
            RequestOptions::BODY => json_encode($fields),
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
            ],
        ];

        $raw = $this->handleResponse(
            $this->httpClient->put("/comment/$id", $options)
        );

        /** @var Comment $comment */
        $comment = $this->hydrator->hydrateObject(Comment::class, $raw);

        return $comment;
    }

    private function handleResponse(ResponseInterface $response): array
    {
        if ($response->getStatusCode() !== 200) {
            throw new ApiException("Response status code is not 200 OK", $response);
        }

        try {
            $json = new JsonSchema(
                json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR)
            );
        } catch (\JsonException) {
            throw new ApiException("Invalid json schema.", $response);
        }

        if ($json->getStatus() === JsonStatuses::FAILED) {
            throw new ApiException("Response status is failed.", $response);
        }

        return $json->getData();
    }
}
