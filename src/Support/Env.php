<?php

namespace UploadAbstraction\Support;

class Env
{
    private static bool $loaded = false;

    public static function get(string $key, mixed $default = null): mixed
    {
        if (!self::$loaded) {
            self::tryLoadDotEnv();
        }

        $value = getenv($key);
        return $value !== false ? $value : $default;
    }

    private static function tryLoadDotEnv(): void
    {
        $root = dirname(__DIR__, 2);
        $envPath = $root . '/.env';

        if (file_exists($envPath)) {
            if (class_exists(\Dotenv\Dotenv::class)) {
                $dotenv = \Dotenv\Dotenv::createImmutable($root);
                $dotenv->safeLoad();
            }
        }

        self::$loaded = true;
    }
}
