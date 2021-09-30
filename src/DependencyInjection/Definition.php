<?php


namespace App\DependencyInjection;

use ReflectionClass;
use ReflectionParameter;

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

    public function newInstance(ContainerInterface $container)
    {
        $reflectionClass = new ReflectionClass($this->id);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return $reflectionClass->newInstance();
        }

        $args = array_map(function (ReflectionParameter $parameter) use ($container) {
            // get dependency class.
            if ($parameter->getClass()) {
                return $container->get($parameter->getClass()->getName());
            }

            // get dependency with default value.
            if ($parameter->isOptional()) {
                if ($parameter->isDefaultValueAvailable()) {
                    return $parameter->getDefaultValue();
                }
            }

            // get dependency register as parameter.
            return $container->getParameter($parameter->getName());

        }, $constructor->getParameters());

        return $reflectionClass->newInstanceArgs($args);
    }
}