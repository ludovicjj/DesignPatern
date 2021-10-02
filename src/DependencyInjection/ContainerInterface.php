<?php


namespace App\DependencyInjection;

use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Return definition (singleton).
     *
     * @param string $id
     * @return Definition
     */
    public function getDefinition(string $id): Definition;

    /**
     * Create definition for asked class.
     * Create definition foreach dependencies of asked class.
     *
     * @param string $id
     */
    public function resolveDefinition(string $id): void;

    /**
     * Register an interface bind to a class.
     *
     * @param string $alias
     * @param string $target
     * @return mixed
     */
    public function addAlias(string $alias, string $target): self;

    /**
     * Register a parameter with his value.
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function addParameter(string $name, $value): self;

    /**
     * Get parameter value.
     *
     * @param string $name
     * @return mixed
     */
    public function getParameter(string $name);

    public function hasParameter(string $name): bool;
}