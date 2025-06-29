<?php
declare(strict_types=1);

namespace crm\src\components\RouteHandler;

use crm\src\components\RouteHandler\common\interfaces\IRoute;

class RouteHandler
{
    /**
     * @param IRoute[] $routes
     */
    public function __construct(
        public readonly string $currentUrl = '',
        private array $routes = [],
        private bool $autoProcessUrl = true,
        private ?IRoute $defaultRoute = null,
        private ?IRoute $errorRoute = null
    ) {}

    /**
     * @return IRoute[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @param IRoute[] $routes
     */
    public function setRoutes(array $routes): void
    {
        $this->routes = $routes;
    }

    public function addRoute(IRoute $route): void
    {
        $this->routes[] = $route;
    }

    /**
     * Выполняет сравнение URL с паттернами маршрутов и вызывает соответствующий обработчик.
     *
     * Паттерн — регулярное выражение без разделителей (//, ## и т.п.) и без флагов.
     *
     * @throws \RuntimeException если маршрут не найден или класс/метод отсутствуют
     */
    public function dispatch(): void
    {
        // $initialBufferLevel = ob_get_level();
        // if ($initialBufferLevel === 0) {
        //     ob_start();
        // }

        try {
            $urlToMatch = $this->autoProcessUrl
                ? $this->processUrl($this->currentUrl)
                : $this->currentUrl;

            foreach ($this->routes as $route) {
                if (preg_match('#' . $route->getPattern() . '#', $urlToMatch, $matches)) {
                    $this->invokeRoute($route, $matches);
                    return;
                }
            }

            if ($this->defaultRoute !== null) {
                $this->invokeRoute($this->defaultRoute, []);
                return;
            }

            throw new \RuntimeException('Маршрут не найден для URL: ' . $this->currentUrl);
        } catch (\Throwable $e) {
            $this->handleDispatchError($e);
        } finally {
            // Закрываем буфер, если мы его открывали
            // while (ob_get_level() > $initialBufferLevel) {
            //     ob_end_flush();
            // }
        }
    }

    /**
     * @todo Реализовать обработку ошибок
     */
    private function handleDispatchError(\Throwable $e): void
    {
        throw new \LogicException('Метод handleDispatchError пока не реализован.');

        $isWarning = $e instanceof \ErrorException
            && in_array($e->getSeverity(), [E_WARNING, E_USER_WARNING]);

        if ($this->errorRoute !== null) {
            // Получаем и очищаем текущий буфер
            // $previousOutput = '';
            // if (ob_get_level() > 0) {
            //     $previousOutput = ob_get_clean();
            // }

            // Выводим ошибку/варнинг (будет первым)
            $this->invokeRoute($this->errorRoute, [0, $e, ['warning' => $isWarning]]);

            // Снова выводим ранее сохраненный вывод
            // echo $previousOutput;

            // if (!$isWarning) {
                // Если это ошибка — завершить выполнение,
                // чтобы не продолжать скрипт после fatal
                // exit;
            // }
            // Для варнинга можно продолжить выполнение, буфер уже восстановлен
        } else {
            throw $e;
        }
    }

    /**
     * Вспомогательный метод для вызова контроллера по маршруту
     * 
     * @param IRoute $route
     * @param array<int|string,mixed> $matches
     * 
     * @throws \RuntimeException
     */
    private function invokeRoute(IRoute $route, array $matches): void
    {
        $className = $route->getClassName();
        $methodName = $route->getMethodName();
        $extraData = $route->getExtraData();

        if (!class_exists($className)) {
            throw new \RuntimeException("Класс {$className} не найден.");
        }

        $routeParams = array_slice($matches, 1);
        $mergedParams = $this->mergeParams($routeParams, $extraData);

        if ($methodName !== null) {
            $controller = new $className();

            if (!method_exists($controller, $methodName)) {
                throw new \RuntimeException("Метод {$methodName} не найден в классе {$className}.");
            }

            call_user_func_array([$controller, $methodName], $mergedParams);
        } else {
            $reflection = new \ReflectionClass($className);
            $reflection->newInstanceArgs($mergedParams);
        }
    }

    /**
     * Обрабатывает URL, отрезая GET-параметры (всё после ?).
     */
    private function processUrl(string $url): string
    {
        return parse_url($url, PHP_URL_PATH) ?: $url;
    }

    /**
     * Объединяет параметры из URL и extraData в один индексированный массив.
     * 
     * @param array<int|string,mixed> $routeParams
     */
    private function mergeParams(array $routeParams, $extraData): array
    {
        return array_merge($routeParams, array_values($extraData));
    }
}
