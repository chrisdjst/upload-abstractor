<?php

namespace EnvManager\Readers;

use EnvManager\Contracts\EnvironmentInterface;

class GetenvReader implements EnvironmentInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}
