<?php

namespace App\DependencyInjection;

use ReflectionClass;
use ReflectionParameter;

class Container implements ContainerInterface
{
    private $instances = [];

    /** @var Definition[] $definitions */
    private $definitions = [];

    /** @var string[] */
    private $aliases = [];

    /** @var mixed[] */
    private $parameters = [];

    public function get(string $id)
    {
        if (!$this->has($id)) {
            if (!class_exists($id) && !interface_exists($id)) {
                throw new NotFoundException();
            }

            $definition = $this->getDefinition($id);
            $this->instances[$id] = $definition->newInstance($this);
        }

        return $this->instances[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->instances);
    }

    public function getDefinition(string $id): Definition
    {
        if (!array_key_exists($id, $this->definitions)) {
            $this->resolveDefinition($id);
        }

        return $this->definitions[$id];
    }

    public function resolveDefinition(string $id): void
    {
        $reflectionClass = new ReflectionClass($id);
        $dependencies = [];

        // case interface
        if ($reflectionClass->isInterface()) {
            if (!array_key_exists($id, $this->aliases)) {
                throw new NotFoundException();
            }

            $this->resolveDefinition($this->aliases[$id]);
            $this->definitions[$id] = $this->definitions[$this->aliases[$id]];
            return;
        }

        // case constructor
        if ($reflectionClass->getConstructor() !== null) {
            $parameters = $reflectionClass->getConstructor()->getParameters();

            $classParameters = array_filter($parameters, function (ReflectionParameter $parameter) {
                return $parameter->getClass();
            });

            $dependencies = array_map(function (ReflectionParameter $classParameter) {
                return $this->getDefinition($classParameter->getClass()->getName());
            }, $classParameters);
        }

        $alias = array_filter($this->aliases, function ($alias) use ($id) {
            return $alias === $id;
        });

        $this->definitions[$id] = new Definition($id, $alias, $dependencies);
    }

    public function addAlias(string $alias, string $target): ContainerInterface
    {
        $this->aliases[$alias] = $target;
        return $this;
    }

    public function addParameter(string $name, $value): ContainerInterface
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws NotFoundException
     */
    public function getParameter(string $name)
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new NotFoundException();
        }
        return $this->parameters[$name];
    }
}