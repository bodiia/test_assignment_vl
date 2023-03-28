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
use TestAssignment\Model\Comment;

/**
 * @covers CommentsClient
 */
final class CommentsClientTest extends TestCase
{
    private MockHandler $mockHandler;

    private CommentsClientInterface $commentsClient;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $this->commentsClient = new CommentsClient(
            new Client(['handler' => $handlerStack, RequestOptions::HTTP_ERRORS => false])
        );
    }

    public function testGetComments(): void
    {
        $expected = [
            ['id' => 1, 'name' => 'test', 'text' => 'test'],
            ['id' => 2, 'name' => 'test', 'text' => 'test'],
        ];

        $json = [
            'status' => 'success',
            'data' => $expected
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($json)));

        $comments = $this->commentsClient->get();

        $this->assertEquals($expected[0]['id'], $comments[0]->getId());
        $this->assertEquals($expected[0]['text'], $comments[0]->getText());
        $this->assertEquals($expected[0]['name'], $comments[0]->getName());

        $this->assertEquals($expected[1]['id'], $comments[1]->getId());
        $this->assertEquals($expected[1]['text'], $comments[1]->getText());
        $this->assertEquals($expected[1]['name'], $comments[1]->getName());
    }

    public function testCreateComment(): void
    {
        $expected = ['id' => 1, 'name' => 'test', 'text' => 'test'];

        $attributes = ['name' => 'test', 'text' => 'test'];

        $json = [
            'status' => 'success',
            'data' => $expected
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($json)));

        $comment = $this->commentsClient->create($attributes);

        $this->assertEquals($expected['id'], $comment->getId());
        $this->assertEquals($expected['text'], $comment->getText());
        $this->assertEquals($expected['name'], $comment->getName());
    }

    public function testUpdateComment(): void
    {
        $comment = ['id' => 1, 'name' => 'test', 'text' => 'test'];

        $attributes = ['name' => 'test_1', 'text' => 'test_1'];

        $json = [
            'status' => 'success',
            'data' => [...$comment, ...$attributes]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($json)));

        $updatedComment = $this->commentsClient->update($comment['id'], $attributes);

        $this->assertEquals($attributes['name'], $updatedComment->getName());
        $this->assertEquals($attributes['text'], $updatedComment->getText());
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
                        'status' => 'failed',
                        'data' => []
                    ];
                    $this->mockHandler->append(new Response(200, [], json_encode($json)));
                    $this->commentsClient->get();
                }
            ]
        ];
    }
}
