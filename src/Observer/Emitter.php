<?php

namespace App\Observer;

class Emitter
{
    private static $_instance;

    /**
     * @var Listener[][]
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
     * Register event.
     * Associates any valid PHP callable to an event with priority.
     *
     * @param string $event Event name
     * @param callable $callable Callable to run for this event
     * @param int $priority The priority to calling callback, default 0.
     */
    public function on(string $event, callable $callable, int $priority = 0): void
    {
        $listener = new Listener($callable, $priority);

        $this->listeners[$event][] = $listener;
        $this->sortDescListener($event);
    }

    /**
     * Send event.
     * Execute callable(s) for a given event.
     *
     * @param string $event Event name
     * @param mixed ...$args Parameters for callable
     */
    public function emit(string $event, ...$args): void
    {
        if (array_key_exists($event, $this->listeners)) {
            foreach ($this->listeners[$event] as $listener) {
                $listener->handle($args);
            }
        }
    }

    /**
     * Desc sort listener by priority.
     *
     * @param string $event
     */
    private function sortDescListener(string $event): void
    {
        uasort($this->listeners[$event], function ($a, $b) {
            if ($a->getPriority() === $b->getPriority()) {
                return 0;
            }
           return $a->getPriority() < $b->getPriority();
        });
    }
}