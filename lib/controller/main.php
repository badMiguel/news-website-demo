<?php

declare(strict_types=1);

class Application
{
    private Model $model;
    private Paginator $paginator;
    private array $currNewsList;

    public function __construct(Model $model, Paginator $paginator)
    {
        $this->model = $model;
        $this->paginator = $paginator;
        $this->currNewsList = $this->paginator->start();
    }

    /**
     *
     * Use this method to render a view inside layout.php
     * - $viewName is the file name of the views (without ".php") e.g. home 
     * - $data is associative array to pass data to views
     *
     */
    public function render(string $viewName, array $data): void
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

        $totalPages = $this->paginator->getTotalPages();

        if (isset($_GET["page"]) || isset($_GET["display"])) {
            if (isset($_GET["display"])) {
                $this->paginator->changeAmountToDisplay((int) $_GET["display"]);
                $totalPages = $this->paginator->getTotalPages();

                if ($this->paginator->currentPage > $totalPages) {
                    $this->paginator->currentPage = $totalPages;
                }
            }

            $page = $this->paginator->currentPage;
            if ($_GET["page"] > $totalPages) {
            } else {
                $page = (int) $_GET["page"];
            }

            $this->currNewsList = $this->paginator->skipToPage($page);
        }
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

    public function news(): void
    {
        $newsDetails = $this->model->getNewsDetails((int) $_GET["id"]);

        $data = [
            "newsDetails" => $newsDetails,
        ];

        $this->render("news_details", $data);
    }

    public function createNews(): void
    {
        $this->checkPrivilege(JOURNALIST);

        $data = [
            "title" => "Create News"
        ];
        $this->render("create_news", $data);
    }

    public function createNewsSubmit(): void
    {
        $this->checkPrivilege(JOURNALIST);

        /**
         * TODO 
         * - need to implement the csrf on the forms too
         *
         * if (
         *     !isset($_POST["csrf_token"], $_SESSION["csrf_token"]) ||
         *     $_POST["csrf_token"] !== $_SESSION["csrf_token"]
         * ) {
         *     session_start();
         *     $_SESSION["newsCreateStatus"] = false;
         *     $_SESSION["newsCreateError"] = "Invalid CSRF token.";
         *     session_write_close();
         * 
         *     header("Location: /news/create");
         *     exit();
         * }
         */


        if (
            !isset($_POST["news_title"]) || $_POST["news_title"] === "" ||
            !isset($_POST["news_subtitle"]) || $_POST["news_subtitle"] === "" ||
            !isset($_POST["body"]) || $_POST["body"] === ""
        ) {
            session_start();
            $_SESSION["newsCreateStatus"] = false;
            session_write_close();

            header("Location: /news/create");
            exit();
        }

        if (!isset($_SESSION['user_id'])) {
            session_start();
            $_SESSION["newsCreateStatus"] = false;
            session_write_close();

            header("Location: /news/create");
            exit();
        }

        try {

            $newsTitle = $_POST["news_title"];
            $newsSummary = $_POST["news_subtitle"];
            $newsBody = $_POST["body"];

            $this->model->addNewsToDB(
                newsTitle: $newsTitle,
                newsSummary: $newsSummary,
                newsBody: $newsBody
            );

            session_start();
            $_SESSION["newsCreateStatus"] = true;
            session_write_close();

            header("Location: /news/create");
            exit();
        } catch (Exception $e) {
            session_start();
            $_SESSION["newsCreateStatus"] = false;
            $_SESSION["newsCreateError"] = $e->getMessage();
            session_write_close();

            header("Location: /news/create");
            exit();
        }
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);


            if ($username === null || $password === null) {
                $error = "Username and password are required.";
                $this->render("login", ['error' => $error]);
                return;
            }

            $user = $this->model->getUserByUsername($username);
            if ($user && password_verify($password . $user['salt'], $user['hashed_password'])) {
                // login
                session_start();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['user_name'];
                $_SESSION['privilege'] = $user['privilege'];
                session_write_close();

                header('Location: /');
                exit;
            } else {
                // fail login
                $error = "Invalid username or password";
                $this->render("login", ['error' => $error]);
            }
        } else {
            $this->render("login", []);
        }
    }

    public function logout(): void
    {
        session_start();
        session_destroy();
        header('Location: /');
        exit;
    }

    public function checkPrivilege(int $requiredPrivilege): void
    {
        session_start();
        if (!isset($_SESSION['privilege']) || $_SESSION['privilege'] < $requiredPrivilege) {
            // session_write_close();
            header('Location: /login');
            exit;
        }
        session_write_close();
    }

    public function editNews(): void
    {
        $this->checkPrivilege(EDITOR);

        $newsDetails = $this->model->getNewsDetails((int) $_GET["id"]);

        $data = [
            "title" => "Edit News",
            "newsDetails" => $newsDetails,
        ];
        $this->render("edit_news", $data);
    }

    public function editNewsSubmit(): void
    {
        $this->checkPrivilege(EDITOR);

        if (
            !isset($_POST["news_id"]) || $_POST["news_id"] === "" ||
            !isset($_POST["news_title"]) || $_POST["news_title"] === "" ||
            !isset($_POST["news_subtitle"]) || $_POST["news_subtitle"] === "" ||
            !isset($_POST["body"]) || $_POST["body"] === ""
        ) {
            session_start();
            $_SESSION["newsEditStatus"] = false;
            session_write_close();

            header("Location: /news/edit?id=" . $_POST["news_id"]);
            exit();
        }

        $this->model->updateNewsInDB((int) $_POST["news_id"]);

        session_start();
        $_SESSION["newsEditStatus"] = true;
        session_write_close();

        header("Location: /news/edit?id=" . $_POST["news_id"]);
        exit();
    }

    public function deleteNews(): void
    {
        $this->checkPrivilege(EDITOR);

        $newsId = (int) $_GET["id"];
        $this->model->deleteNewsFromDB($newsId);

        header("Location: /");
        exit();
    }

    public function addComment(): void
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newsId = (int) $_POST['news_id'];
            $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

            if ($comment === null || $comment === "") {
                header("Location: /news?id=" . $newsId);
                exit;
            }

            $this->model->addCommentToDB($newsId, $_SESSION['user_id'], $comment);
        }

        header("Location: /news?id=" . $newsId);
        exit;
        session_write_close();
    }

    public function pageNotFound(): void
    {
        $this->render("404", []);
    }
}
