<?php


namespace Tests\DependencyInjection\Classes;


class Bar
{
    public function __construct(Foo $foo, string $firstname = "john")
    {
    }
}