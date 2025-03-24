<?php

namespace EnvManager\Contracts;

interface EnvironmentInterface
{
    public function get(string $key, mixed $default = null): mixed;
}
