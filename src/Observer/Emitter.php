<?php

namespace App\Observer;

class Emitter
{
    private static $_instance;

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
}