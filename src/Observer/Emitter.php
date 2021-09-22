<?php

namespace App\Observer;

class Emitter
{
    private static $_instance;

    /**
     * @var callable[][]
     */
    private $listeners = [];

    /**
     * Return same instance of Emitter (singleton)
     *
     * @return static
     */
    public static function getInstance(): self
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Setup event
     *
     * @param string $event Event name
     * @param callable $callable Callable to run for this event
     */
    public function on(string $event, callable $callable)
    {
        $this->listeners[$event][] = $callable;
    }

    /**
     * Send event
     *
     * @param string $event Event name
     * @param mixed ...$args Parameters for callable
     */
    public function emit(string $event, ...$args)
    {
        if (array_key_exists($event, $this->listeners)) {
            foreach ($this->listeners[$event] as $listener) {
                call_user_func_array($listener, $args);
            }
        }
    }
}