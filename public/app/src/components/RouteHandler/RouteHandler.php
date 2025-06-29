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
        private ?IRoute $defaultRoute = null
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
     * Выполняет сравнение и вызывает соответствующий обработчик.
     *
     * @return void
     * @throws \RuntimeException если маршрут не найден или класс/метод отсутствуют
     */
    public function dispatch(): void
    {
        $urlToMatch = $this->autoProcessUrl
            ? $this->processUrl($this->currentUrl)
            : $this->currentUrl;

        foreach ($this->routes as $route) {
            if (preg_match($route->getUrl(), $urlToMatch, $matches)) {
                $this->invokeRoute($route, $matches);
                return;
            }
        }

        // Если маршрут не найден, пробуем маршрут по умолчанию
        if ($this->defaultRoute !== null) {
            $this->invokeRoute($this->defaultRoute, []);
            return;
        }

        throw new \RuntimeException('Маршрут не найден для URL: ' . $this->currentUrl);
    }

    /**
     * Вспомогательный метод для вызова контроллера по маршруту
     * 
     * @param IRoute $route
     * @param array $matches
     * @return void
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
     */
    private function mergeParams(array $routeParams, $extraData): array
    {
        return array_merge($routeParams, array_values($extraData));
    }
}
