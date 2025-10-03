<?php

declare(strict_types=1);

namespace MoneyFlow\Config;

use RuntimeException;

class Config
{
    private array $values;

    public function __construct(string $basePath)
    {
        $envFile = $basePath . '/.env';
        if (!file_exists($envFile)) {
            $envFile = $basePath . '/.env.example';
        }

        $this->values = $this->parseEnvFile($envFile);
    }

    private function parseEnvFile(string $path): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new RuntimeException('Unable to read environment file.');
        }

        $config = [];
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode('=', $line, 2));
            $config[$key] = $value;
        }

        return $config;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->values[$key] ?? $default;
    }
}
