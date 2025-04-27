<?php

declare(strict_types=1);

class Application
{
    private function render(string $viewName) {}

    public function index(): void
    {
        require VIEWS . "home.php";
    }

    public function pageNotFound(): void
    {
        require VIEWS . "404.php";
    }
}
