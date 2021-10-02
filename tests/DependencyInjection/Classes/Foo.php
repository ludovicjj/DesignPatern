<?php


namespace Tests\DependencyInjection\Classes;


use Tests\DependencyInjection\Classes\Interfaces\FooInterface;

class Foo implements FooInterface
{
    private $firstname;

    public function __construct(string $firstname = "john")
    {
        $this->firstname = $firstname;
    }
}