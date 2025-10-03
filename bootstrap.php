<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $prefix = 'MoneyFlow\\';
    if (str_starts_with($class, $prefix)) {
        $relative = substr($class, strlen($prefix));
        $relative = str_replace('\\', DIRECTORY_SEPARATOR, $relative);
        $path = __DIR__ . '/app/' . $relative . '.php';
        if (file_exists($path)) {
            require $path;
        }
    }
});

$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}
