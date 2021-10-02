<?php


namespace Tests\DependencyInjection\Classes;


use Tests\DependencyInjection\Classes\Interfaces\FooInterface;
use Tests\DependencyInjection\Classes\Interfaces\SolInterface;

class Bar
{
    public function __construct(FooInterface $foo, int $age, SolInterface $sol)
    {
    }
}