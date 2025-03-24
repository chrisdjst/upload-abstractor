<?php

namespace EnvManager\Readers;

use EnvManager\Contracts\EnvironmentInterface;

class ServerEnvReader implements EnvironmentInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SERVER[$key] ?? $default;
    }
}
