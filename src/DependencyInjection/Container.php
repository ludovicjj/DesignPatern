<?php

namespace App\DependencyInjection;

use Psr\Container\ContainerExceptionInterface;
use ReflectionClass;
use ReflectionParameter;

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

        if ($this->hasConstructor($reflectionClass)) {
            $parameters = $this->getConstructorParameters($reflectionClass);
            $dependencies = (!empty($parameters)) ? $this->getDefinitionParameters($parameters): [];
        } else {
            $dependencies = [];
        }

        $alias = array_filter($this->aliases, function ($alias) use ($id) {
            return $alias === $id;
        });

        $this->definitions[$id] = new Definition($id, $alias, $dependencies);
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

    private function hasConstructor(ReflectionClass $reflectionClass): bool
    {
        return $reflectionClass->getConstructor() !== null;
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return array|ReflectionParameter[]
     */
    private function getConstructorParameters(ReflectionClass $reflectionClass): array
    {
        $reflectionMethod = $reflectionClass->getConstructor();
        return $reflectionMethod->getParameters();
    }

    private function getDefinitionParameters(array $reflectionParameters): array
    {
        return array_map(function (ReflectionParameter $parameter) {
            return $this->getDefinition($parameter->getClass()->getName());
        }, array_filter($reflectionParameters, function ($parameter) {
            return $parameter->getClass();
        }));
    }
}