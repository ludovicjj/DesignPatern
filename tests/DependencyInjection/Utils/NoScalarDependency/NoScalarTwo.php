<?php


namespace Tests\DependencyInjection\Utils\NoScalarDependency;


use Tests\DependencyInjection\Utils\Interfaces\NoScalarOneInterface;
use Tests\DependencyInjection\Utils\Interfaces\NoScalarTwoInterface;

class NoScalarTwo implements NoScalarTwoInterface
{
    public function __construct(NoScalarOneInterface $arg)
    {
    }
}