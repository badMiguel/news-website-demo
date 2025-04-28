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

        /**
         *
         * Define URL routes: "path" => [which object, which method]
         * Add more routes here as needed, for example:
         *
         *   "/" => [$this->app, "index"]
         *
         * This will run index() method from $this->app when user visits "/"
         *
         */
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
