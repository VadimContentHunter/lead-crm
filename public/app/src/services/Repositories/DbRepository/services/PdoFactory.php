<?php

namespace crm\src\services\Repositories\DbRepository\services;

use PDO;
use PDOException;

class PdoFactory
{
    /**
     * Создаёт и возвращает готовый к использованию PDO объект.
     *
     * @param  array<string,mixed> $config Конфигурация подключения:
     *                                     ```php
     *                                     [
     *                                     'host' => string,
     *                                     'db' => string,
     *                                     'user' => string,
     *                                     'pass' => string,
     *                                     'charset' => string //(опционально, по умолчанию utf8mb4)
     *                                     ]
     *                                     ```
     * @return PDO
     * @throws PDOException
     */
    public static function create(array $config): PDO
    {
        $host = $config['host'] ?? 'localhost';
        $db = $config['db'] ?? '';
        $user = $config['user'] ?? '';
        $pass = $config['pass'] ?? '';
        $charset = $config['charset'] ?? 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return new PDO($dsn, $user, $pass, $options);
    }
}
