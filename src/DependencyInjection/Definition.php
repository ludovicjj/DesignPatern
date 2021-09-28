<?php


namespace App\DependencyInjection;

use ReflectionClass;

class Definition
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $alias;

    public function __construct(string $id, array $alias)
    {
        $this->id = $id;
        $this->alias = $alias;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAlias(): array
    {
        return $this->alias;
    }

    public function newInstance()
    {
        $reflectionClass = new ReflectionClass($this->id);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return $reflectionClass->newInstance();
        }

        return $reflectionClass->newInstanceArgs([]);
    }
}