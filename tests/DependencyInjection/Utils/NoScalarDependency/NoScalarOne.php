<?php


namespace Tests\DependencyInjection\Utils\NoScalarDependency;


use Tests\DependencyInjection\Utils\NoDependency\NoDependency;

class NoScalarOne
{
    public function __construct(NoDependency $arg1)
    {
    }
}