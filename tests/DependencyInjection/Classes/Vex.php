<?php


namespace Tests\DependencyInjection\Classes;

use Tests\DependencyInjection\Classes\Interfaces\BarInterface;

class Vex
{
    public function __construct(BarInterface $bar)
    {
    }
}