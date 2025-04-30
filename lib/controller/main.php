<?php

declare(strict_types=1);

class Application
{
    private Model $model;
    private Paginator $paginator;
    private array $newsList;
    private array $currNewsList;

    public function __construct(Model $model, Paginator $paginator)
    {
        $this->model = $model;
        $this->paginator = $paginator;
        $this->newsList = $this->model->getAllNews();
        $this->currNewsList = $this->paginator->start($this->newsList);
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
        session_start();
        $this->paginator->currentPage = isset($_SESSION["currentPage"]) ? $_SESSION["currentPage"] : 1;
        session_write_close();


        if (isset($_GET["prev"])) {
            $this->currNewsList = $this->paginator->prevPage();
        } else if (isset($_GET["next"])) {
            $this->currNewsList = $this->paginator->nextPage();
        } else if (isset($_GET["page"])) {
            $page = (int) $_GET["page"];
            $this->currNewsList = $this->paginator->skipToPage($page);
        }

        $totalPages = $this->paginator->getTotalPages();
        $pageInfo = $this->paginator->getPageRange();

        $data = [
            "currNewsList" => $this->currNewsList,
            "currentPage" => $this->paginator->currentPage,
            "totalPages" => $totalPages,
            "pageStart" => $pageInfo[0],
            "pageEnd" => $pageInfo[1],
        ];

        $this->render("home", $data);
    }

    public function pageNotFound(): void
    {
        $this->render("404", []);
    }
}
