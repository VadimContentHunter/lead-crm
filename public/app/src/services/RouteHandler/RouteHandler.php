<?php

declare(strict_types=1);

namespace crm\src\services\RouteHandler;

use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use  crm\src\services\RouteHandler\common\interfaces\IRoute;

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
        private ?IRoute $errorRoute = null,
        private LoggerInterface $logger = new NullLogger()
    ) {
    }

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
     * @todo Можно добавить работу с буфером Не забывать про ob_get_level();
     *
     * @throws \RuntimeException если маршрут не найден или класс/метод отсутствуют
     */
    public function dispatch(): void
    {
        $this->logger->info("Dispatch started", ['url' => $this->currentUrl]);

        try {
            $urlToMatch = $this->autoProcessUrl
                ? $this->processUrl($this->currentUrl)
                : $this->currentUrl;

            foreach ($this->routes as $route) {
                if (preg_match('#' . $route->getPattern() . '#', $urlToMatch, $matches)) {
                    $this->logger->info("Route matched", ['pattern' => $route->getPattern(), 'url' => $urlToMatch]);
                    $this->invokeRoute($route, $matches);
                    return;
                }
            }

            if ($this->defaultRoute !== null) {
                $this->logger->info("Default route invoked", ['url' => $urlToMatch]);
                $this->invokeRoute($this->defaultRoute, []);
                return;
            }

            throw new \RuntimeException('Маршрут не найден для URL: ' . $this->currentUrl);
        } catch (\Throwable $e) {
            $this->logger->error("Dispatch error", ['exception' => $e]);
            $this->handleDispatchError($e);
        } finally {
        }
    }

    /**
     * @todo Можно добавить работу с буфер
     */
    private function handleDispatchError(\Throwable $e): void
    {
        $isWarning = $e instanceof \ErrorException
            && in_array($e->getSeverity(), [E_WARNING, E_USER_WARNING]);

        if ($this->errorRoute !== null) {
            $this->invokeRoute($this->errorRoute, [0, $e, ['warning' => $isWarning]]);
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

        $extraData = $route->getExtraData();             // Данные для конструктора
        $methodExtraData = $route->getMethodExtraData(); // Данные для метода

        if (!class_exists($className)) {
            throw new \RuntimeException("Класс {$className} не найден.");
        }

        $routeParams = array_slice($matches, 1);
        $constructorParams = array_merge(array_values($extraData), $routeParams);
        $methodParams = array_merge(array_values($methodExtraData), $routeParams);

        // Создаём контроллер через Reflection
        $reflection = new \ReflectionClass($className);
        $controller = $reflection->newInstanceArgs($constructorParams);

        if ($methodName !== null) {
            if (!method_exists($controller, $methodName)) {
                throw new \RuntimeException("Метод {$methodName} не найден в классе {$className}.");
            }

            // Вызываем метод
            // call_user_func_array([$controller, $methodName], $methodParams);

            $controller->$methodName(...$methodParams);
        }
    }

    /**
     * Обрабатывает URL, отрезая GET-параметры (всё после ?).
     */
    private function processUrl(string $url): string
    {
        return parse_url($url, PHP_URL_PATH) ?: $url;
    }
}
