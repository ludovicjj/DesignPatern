<?php


namespace App\DependencyInjection;


use ArrayIterator;

class ParameterErrorList implements \IteratorAggregate, \Countable
{
    private $errors;

    public function __construct($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->errors);
    }

    public function count(): int
    {
        return count($this->errors);
    }
}