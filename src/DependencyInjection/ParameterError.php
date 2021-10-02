<?php


namespace App\DependencyInjection;


class ParameterError
{
    /** @var mixed $required */
    private $required;

    /** @var mixed $given */
    private $given;

    /** @var string $method */
    private $method;

    /** @var string $class */
    private $class;

    /** @var string $parameter */
    private $parameter;

    public function __construct(string $class, string $method, string $parameter, $required, $given)
    {
        $this->class = $class;
        $this->method = $method;
        $this->parameter = $parameter;
        $this->required = $required;
        $this->given = $given;
    }


    public function getMessage() {
        return "{$this->getClass()}::{$this->getMethod()}, parameter {$this->getParameter()} expected {$this->getRequired()} given {$this->getGiven()}";
    }

    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * @return mixed
     */
    private function getRequired()
    {
        return $this->required;
    }

    private function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return mixed
     */
    private function getGiven()
    {
        return $this->given;
    }

    private function getMethod(): string
    {
        return $this->method;
    }
}