<?php

declare(strict_types=1);

namespace TestAssignment\Hydrator;

final class Hydrator
{
    public function hydrateObject(string $target, array $raw): object
    {
        $reflection = new \ReflectionClass($target);
        $reflectionConstructor = $reflection->getConstructor();

        $args = [];
        foreach ($reflectionConstructor->getParameters() as $parameter) {
            $args[$parameter->getName()] = $raw[$parameter->getName()];
        }
        return $reflection->newInstanceArgs($args);
    }

    public function hydrateObjects(string $target, array $rawObjects): array
    {
        $objects = [];
        foreach ($rawObjects as $raw) {
            $objects[] = $this->hydrateObject($target, $raw);
        }
        return $objects;
    }
}
