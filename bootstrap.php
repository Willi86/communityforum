<?php

declare(strict_types=1);

use App\Database\Database;

require_once __DIR__ . '/src/Support/helpers.php';

spl_autoload_register(
    static function (string $class): void {
        $prefix = 'App\\';

        if (!str_starts_with($class, $prefix)) {
            return;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = __DIR__ . '/src/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($file)) {
            require_once $file;
        }
    }
);

$configFile = __DIR__ . '/config/config.php';

if (!is_file($configFile)) {
    throw new RuntimeException(
        'Missing config/config.php. Copy config/config.example.php to config/config.php and add your local database settings.'
    );
}

$config = require $configFile;

if (!isset($config['database']) || !is_array($config['database'])) {
    throw new RuntimeException('The database configuration is missing.');
}

$pdo = Database::connect($config['database']);
