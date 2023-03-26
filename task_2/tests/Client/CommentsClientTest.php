<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TestAssignment\Client\CommentsClient;
use TestAssignment\Client\CommentsClientInterface;
use TestAssignment\Exception\ApiException;
use TestAssignment\Hydrator\Hydrator;
use TestAssignment\Model\Comment;
use TestAssignment\Schema\JsonStatuses;

/**
 * @covers CommentsClient
 */
final class CommentsClientTest extends TestCase
{
    private MockHandler $mockHandler;

    private CommentsClientInterface $commentsClient;

    private Hydrator $hydrator;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $this->commentsClient = new CommentsClient(
            new Client(['handler' => $handlerStack, RequestOptions::HTTP_ERRORS => false])
        );
        $this->hydrator = new Hydrator();
    }

    public function testGetComments(): void
    {
        $expected = [
            ['id' => 1, 'name' => 'test', 'text' => 'test'],
            ['id' => 2, 'name' => 'test', 'text' => 'test'],
        ];

        $json = [
            'status' => JsonStatuses::SUCCESS->value,
            'data' => $expected
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($json)));

        $this->assertEquals(
            $this->hydrator->hydrateObjects(Comment::class, $expected),
            $this->commentsClient->get(),
        );
    }

    public function testCreateComment(): void
    {
        $expected = ['id' => 1, 'name' => 'test', 'text' => 'test'];

        $attributes = ['name' => 'test', 'text' => 'test'];

        $json = [
            'status' => JsonStatuses::SUCCESS->value,
            'data' => $expected
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($json)));

        $this->assertEquals(
            $this->hydrator->hydrateObject(Comment::class, $expected),
            $this->commentsClient->create($attributes),
        );
    }

    public function testUpdateComment(): void
    {
        $expected = ['id' => 1, 'name' => 'test', 'text' => 'test'];

        $attributes = ['name' => 'test', 'text' => 'test'];

        $json = [
            'status' => JsonStatuses::SUCCESS->value,
            'data' => $expected
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($json)));

        $this->assertEquals(
            $this->hydrator->hydrateObject(Comment::class, $expected),
            $this->commentsClient->update($expected['id'], $attributes),
        );
    }

    #[DataProvider('responsesForTriggerException')]
    public function testHandleResponseException(\Closure $closure)
    {
        $this->expectException(ApiException::class);
        $closure->call($this);
    }

    public static function responsesForTriggerException(): array
    {
        return [
            [
                function (): void {
                    $this->mockHandler->append(new Response(500));
                    $this->commentsClient->get();
                }
            ],
            [
                function (): void {
                    $json = [
                        'status' => JsonStatuses::FAILED->value,
                        'data' => []
                    ];
                    $this->mockHandler->append(new Response(200, [], json_encode($json)));
                    $this->commentsClient->get();
                }
            ]
        ];
    }
}
