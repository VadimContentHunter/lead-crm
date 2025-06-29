<?php

namespace crm\src\services;

use Exception;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;

class LoggerFactory
{
    /**
     * Создаёт логгер с ротацией и двумя путями для логов.
     *
     * @param  string $name       Имя логгера.
     * @param  string|null $baseLogDir Абсолютный путь до корня логов (по умолчанию /logs).
     * @param  int $maxFiles   Количество дней хранения ротационных файлов.
     * @return LoggerInterface
     * @throws Exception
     */
    public static function createLogger(
        string $name = 'app',
        ?string $baseLogDir = null,
        int $maxFiles = 7
    ): LoggerInterface {
        date_default_timezone_set('Etc/GMT-3');
        $logger = new Logger($name);

        // Базовая директория логов
        if ($baseLogDir === null) {
            $baseLogDir = __DIR__ . '/logs';
        }

        $infoDir = $baseLogDir . '/info';
        $errorDir = $baseLogDir . '/errors';

        self::ensureDirectory($infoDir);
        self::ensureDirectory($errorDir);

        $outputFormat = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
        $formatter = new LineFormatter($outputFormat, 'Y-m-d H:i:s', true, true);

        // DEBUG и выше в info/
        $infoHandler = new RotatingFileHandler("{$infoDir}/app_info.log", $maxFiles, Level::Debug);
        $infoHandler->setFormatter($formatter);

        // WARNING и выше в errors/
        $errorHandler = new RotatingFileHandler("{$errorDir}/app_errors.log", $maxFiles, Level::Warning);
        $errorHandler->setFormatter($formatter);

        $logger->pushHandler($infoHandler);
        $logger->pushHandler($errorHandler);

        return $logger;
    }

    /**
     * Проверяет наличие директории, создаёт если её нет.
     *
     * @param  string $path
     * @throws Exception
     */
    private static function ensureDirectory(string $path): void
    {
        if (!is_dir($path) && !mkdir($path, 0777, true) && !is_dir($path)) {
            throw new Exception("Не удалось создать директорию: {$path}");
        }
    }
}
