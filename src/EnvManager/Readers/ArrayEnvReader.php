<?php

namespace EnvManager\Readers;

use EnvManager\Contracts\EnvironmentInterface;

class ArrayEnvReader implements EnvironmentInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }
}
