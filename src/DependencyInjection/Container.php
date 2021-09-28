<?php

namespace App\DependencyInjection;

use Psr\Container\ContainerExceptionInterface;
use ReflectionClass;

class Container implements ContainerInterface
{
    private $instances = [];

    public function get(string $id)
    {
        if (!$this->has($id)) {

            if (!class_exists($id) && !interface_exists($id)) {
                throw new NotFoundException();
            }

            $reflectionClass = new ReflectionClass($id);
            if ($reflectionClass->isInstantiable()) {
                $this->instances[$id] = $reflectionClass->newInstance();
            }
        }

        return $this->instances[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->instances);
    }
}