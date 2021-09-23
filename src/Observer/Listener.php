<?php


namespace App\Observer;


class Listener
{
    /**
     * @var callable $callable
     */
    private $callable;

    /**
     * @var int $priority
     */
    private $priority;

    /**
     * Listener constructor.
     * @param callable $callable
     * @param int $priority
     */
    public function __construct(callable $callable, int $priority)
    {
        $this->callable = $callable;
        $this->priority = $priority;
    }

    /**
     * @param array $args
     * @return mixed
     */
    public function handle(array $args)
    {
        return call_user_func_array($this->callable, $args);
    }
}