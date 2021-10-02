<?php


namespace Tests\DependencyInjection\Utils\NoScalarDependency;


use Tests\DependencyInjection\Utils\Interfaces\NoScalarFourInterface;
use Tests\DependencyInjection\Utils\Interfaces\NoScalarThreeInterface;

class NoScalarFour implements NoScalarFourInterface
{
    public function __construct(NoScalarThreeInterface $arg)
    {
    }
}