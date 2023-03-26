<?php

declare(strict_types=1);

namespace Tests\Hydrator;

use PHPUnit\Framework\TestCase;
use TestAssignment\Hydrator\Hydrator;

/** @covers Hydrator */
final class HydratorTest extends TestCase
{
    public function testHydrate(): void
    {
        $hydrator = new Hydrator();
        $dummy = $hydrator->hydrateObject(Dummy::class, $expected = ['id' => 1, 'value' => 'value']);

        $this->assertInstanceOf(Dummy::class, $dummy);
        $this->assertEquals($dummy->getId(), $expected['id']);
        $this->assertEquals($dummy->getValue(), $expected['value']);
    }
}

// phpcs:ignore
class Dummy
{
    public function __construct(
        private readonly int $id,
        private readonly string $value
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}