<?php

declare(strict_types=1);

class Application
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     *
     * Use this method to render a view inside layout.php
     * - $viewName is the file name of the views (without ".php") e.g. home 
     * - $data is associative array to pass data to views
     *
     */
    public function render(string $viewName, array $data)
    {
        $data['viewPath'] = VIEWS . $viewName . ".php";
        extract($data);
        require_once VIEWS . "layout.php";
    }

    public function index(): void
    {
        $this->render("home", ["foo" => "bar"]);
    }

    public function pageNotFound(): void
    {
        $this->render("404", []);
    }
}
