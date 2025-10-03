<?php

declare(strict_types=1);

namespace MoneyFlow\Services;

use MoneyFlow\Config\Config;
use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private PDO $connection;

    public function __construct(Config $config)
    {
        $host = $config->get('DB_HOST', '127.0.0.1');
        $port = $config->get('DB_PORT', '3306');
        $database = $config->get('DB_DATABASE', 'moneyflow');
        $username = $config->get('DB_USERNAME', 'root');
        $password = $config->get('DB_PASSWORD', '');
        $charset = $config->get('DB_CHARSET', 'utf8mb4');
        $collation = $config->get('DB_COLLATION', 'utf8mb4_unicode_ci');

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $database, $charset);

        try {
            $this->connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => sprintf('SET NAMES %s COLLATE %s', $charset, $collation),
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Unable to connect to the database: ' . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
