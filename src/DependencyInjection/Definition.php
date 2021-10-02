<?php


namespace App\DependencyInjection;

use App\DependencyInjection\Exception\ContainerException;
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

    public function newInstance(ContainerInterface $container)
    {
        $reflectionClass = new ReflectionClass($this->id);
        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return $reflectionClass->newInstance();
        }

        $args = array_map(function (ReflectionParameter $reflectionParameter) use ($container) {
            // get dependency class.
            if ($reflectionParameter->getClass()) {
                if ($container->hasParameter($reflectionParameter->getName())) {
                    return $container->getParameter($reflectionParameter->getName());
                }
                return $container->get($reflectionParameter->getClass()->getName());
            }

            // get dependency with default value.
            if ($reflectionParameter->isOptional()) {
                if ($reflectionParameter->isDefaultValueAvailable()) {
                    if ($container->hasParameter($reflectionParameter->getName())) {
                        return $container->getParameter($reflectionParameter->getName());
                    }
                    return $reflectionParameter->getDefaultValue();
                }
            }

            // get dependency register as parameter.
            return $container->getParameter($reflectionParameter->getName());

        }, $constructor->getParameters());


        $validator = new Validator($this->id);
        $errorList = $validator->validConstructorParameters($constructor->getParameters(), $args);
        if ($errorList->count() > 0) {
            /** @var ParameterError $error */
            foreach ($errorList as $error) {
                throw new ContainerException($error->getMessage());
            }
        }


        return $reflectionClass->newInstanceArgs($args);
    }
}