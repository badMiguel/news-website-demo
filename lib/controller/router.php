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
            "/"                     => [$this->app, "index"],
            "/home"                 => [$this->app, "index"],
            "/world"                => [$this->app, "index"],
            "/politics"             => [$this->app, "index"],
            "/business"             => [$this->app, "index"],
            "/technology"           => [$this->app, "index"],
            "/entertainment"        => [$this->app, "index"],
            "/sports"               => [$this->app, "index"],

            "/news"                 => [$this->app, "news"],
            "/news/create"          => [$this->app, "createNews"],
            "/news/create/submit"   => [$this->app, "createNewsSubmit"],
            "/news/edit"            => [$this->app, "editNews"],
            "/news/edit/submit"     => [$this->app, "editNewsSubmit"],
            "/news/delete"          => [$this->app, "deleteNews"],
            "/news/comment/add"     => [$this->app, "addComment"],

            "/login"                => [$this->app, "login"],
            "/logout"               => [$this->app, "logout"],
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
