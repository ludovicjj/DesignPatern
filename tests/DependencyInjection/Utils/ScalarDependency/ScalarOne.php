<?php


namespace Tests\DependencyInjection\Utils\ScalarDependency;

use DateTime;

class ScalarOne
{
    public function __construct(
        string $arg1,
        int $arg2,
        float $arg3,
        array $arg4,
        bool $arg5,
        callable $arg6,
        DateTime $arg7,
        string $arg8 = null,
        $arg9 = "random value"
    )
    {
    }

}