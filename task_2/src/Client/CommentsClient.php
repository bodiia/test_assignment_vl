<?php

declare(strict_types=1);

namespace TestAssignment\Client;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use TestAssignment\DTO\CommentDto;
use TestAssignment\Hydrator\Hydrator;

final class CommentsClient implements CommentsClientInterface
{
    private readonly Hydrator $hydrator;

    private readonly ClientInterface $httpClient;

    public function __construct(?ClientInterface $httpClient)
    {
        $config = [
            'base_uri' => 'https://example.com'
        ];

        $this->httpClient = $httpClient ?? new Client($config);
        $this->hydrator = new Hydrator();
    }

    public function get(array $query = []): array
    {
        $options = [
            RequestOptions::QUERY => $query
        ];

        $response = $this->httpClient->get('/comments', $options);

        $json = json_decode($response->getBody()->getContents(), true);

        return $this->hydrator->hydrateObjects(CommentDto::class, $json['data']);
    }

    public function create(CommentDto $dto): CommentDto
    {
    }

    public function update(CommentDto $dto): CommentDto
    {
    }
}
