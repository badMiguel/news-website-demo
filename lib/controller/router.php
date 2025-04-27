<?php

declare(strict_types=1);

class Router
{
    /** @var array<string, array<Application, string>> */
    private array $routes;
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->routes = [
            "/" => [$this->app, "index"],
        ];
    }

    private function isValidPath(string $path): bool
    {
        return key_exists($path, $this->routes);
    }

    public function dispatch(string $path): void
    {
        if ($this->isValidPath($path)) {
            call_user_func($this->routes[$path]);
        } else {
            $this->app->pageNotFound();
        }
    }
}
