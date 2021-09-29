<?php


namespace App\DependencyInjection;

use ReflectionClass;

class Definition
{
    /**
     * @var string $id
     */
    private $id;

    /**
     * @var array $alias
     */
    private $alias;

    /**
     * @var array $dependencies
     */
    private $dependencies;

    public function __construct(string $id, array $alias, array $dependencies)
    {
        $this->id = $id;
        $this->alias = $alias;
        $this->dependencies = $dependencies;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAlias(): array
    {
        return $this->alias;
    }

    /**
     * @return Definition[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function newInstance()
    {
        $reflectionClass = new ReflectionClass($this->id);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return $reflectionClass->newInstance();
        }

        $args = array_map(function (Definition $dependency) {
            return $dependency->newInstance();
        }, $this->getDependencies());

        return $reflectionClass->newInstanceArgs($args);
    }
}