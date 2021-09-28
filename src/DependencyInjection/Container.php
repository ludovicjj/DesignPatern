<?php

namespace App\DependencyInjection;

use Psr\Container\ContainerExceptionInterface;
use ReflectionClass;

class Container implements ContainerInterface
{
    private $instances = [];

    /**
     * @var Definition[] $definitions
     */
    private $definitions = [];

    private $aliases = [];

    public function get(string $id)
    {
        if (!$this->has($id)) {

            if (!class_exists($id) && !interface_exists($id)) {
                throw new NotFoundException();
            }

            $instance = $this->getDefinition($id)->newInstance();
            $this->instances[$id] = $instance;
        }

        return $this->instances[$id];
    }

    public function getDefinition($id): Definition
    {
        if (!array_key_exists($id, $this->definitions)) {
            $this->makeDefinition($id);
        }

        return $this->definitions[$id];
    }

    public function makeDefinition(string $id): void
    {
        $reflectionClass = new ReflectionClass($id);

        // case interface
        if ($reflectionClass->isInterface()) {
            if (!array_key_exists($id, $this->aliases)) {
                throw new NotFoundException();
            }

            $this->makeDefinition($this->aliases[$id]);
            $this->definitions[$id] = $this->definitions[$this->aliases[$id]];
            return;
        }

        $reflectionMethod = $reflectionClass->getConstructor();

        // case constructor
        if ($reflectionMethod !== null) {
            //Todo resolve construct
        }

        $alias = array_filter($this->aliases, function ($alias) use ($id) {
            return $alias === $id;
        });

        $this->definitions[$id] = new Definition($id, $alias);
    }

    /**
     * Check if giver key already exist
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->instances);
    }

    public function addAlias(string $id, string $class): Container
    {
        $this->aliases[$id] = $class;
        return $this;
    }

    /**
     * @return Definition[]
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }
}