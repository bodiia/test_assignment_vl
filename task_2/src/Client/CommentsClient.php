<?php

declare(strict_types=1);

namespace TestAssignment\Client;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use TestAssignment\Exception\ApiException;
use TestAssignment\Model\Comment;

final readonly class CommentsClient implements CommentsClientInterface
{
    private ClientInterface $httpClient;

    public function __construct(?ClientInterface $httpClient)
    {
        $config = [
            'base_uri' => 'https://example.com',
        ];

        $this->httpClient = $httpClient ?? new Client($config);
    }

    public function get(array $query = []): array
    {
        $options = [
            RequestOptions::QUERY => $query
        ];

        $comments = $this->handleResponse(
            $this->httpClient->get('/comments', $options)
        );

        return array_map(static function ($comment) {
            return new Comment(
                $comment['id'],
                $comment['name'],
                $comment['text']
            );
        }, $comments);
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

        return new Comment(
            $raw['id'],
            $raw['name'],
            $raw['text']
        );
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

        return new Comment(
            $raw['id'],
            $raw['name'],
            $raw['text']
        );
    }

    private function handleResponse(ResponseInterface $response): array
    {
        if ($response->getStatusCode() !== 200) {
            throw new ApiException("Response status code is not 200 OK", $response);
        }

        try {
            $json = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new ApiException("Invalid json schema.", $response);
        }

        if ($json['status'] === 'failed') {
            throw new ApiException("Response status is failed.", $response);
        }

        return $json['data'];
    }
}
