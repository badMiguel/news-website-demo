<?php

declare(strict_types=1);

class Application
{
    private Model $model;
    private Paginator $paginator;
    private CSRF $csrf;
    private array $currNewsList;

    public function __construct(Model $model, Paginator $paginator, CSRF $csrf)
    {
        $this->model = $model;
        $this->paginator = $paginator;
        $this->csrf = $csrf;
        $this->currNewsList = [];
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
        $latestNews = $this->model->getLatestNews();
        $recentNewsPerCategory = [
            "World" => $this->model->getNewsListByCategory(0, 3, "World"),
            "Politics" => $this->model->getNewsListByCategory(0, 3, "Politics"),
            "Business" => $this->model->getNewsListByCategory(0, 3, "Business"),
            "Technology" => $this->model->getNewsListByCategory(0, 3, "Technology"),
            "Entertainment" => $this->model->getNewsListByCategory(0, 3, "Entertainment"),
            "Sports" => $this->model->getNewsListByCategory(0, 3, "Sports"),
        ];

        $data = [
            "latestNews" => $latestNews[0],
            "recentNewsPerCategory" => $recentNewsPerCategory,
            "isHome" => true,
        ];
        $this->render("home", $data);
    }

    public function newsByCategory()
    {
        $path = ucfirst(str_replace("/", "", $_SERVER["PATH_INFO"]));
        $this->currNewsList = $this->paginator->start($path);
        $totalPages = $this->paginator->getTotalPages();
        $pageInfo = $this->paginator->getPageRange();

        if (isset($_GET["page"]) || isset($_GET["display"])) {
            if (isset($_GET["display"])) {
                $this->paginator->changeAmountToDisplay((int) $_GET["display"]);
                $totalPages = $this->paginator->getTotalPages();

                if ($this->paginator->currentPage > $totalPages) {
                    $this->paginator->currentPage = $totalPages;
                }
            }

            $page = $this->paginator->currentPage;
            if (isset($_GET["page"]) && $_GET["page"] <= $totalPages && $_GET["page"] >= 0) {
                $page = (int) $_GET["page"];
            }

            $this->currNewsList = $this->paginator->skipToPage($page, $path);
        }

        $data = [
            "currNewsList" => $this->currNewsList,
            "totalPages" => $totalPages,
            "currentPage" => $this->paginator->currentPage,
            "pageStart" => $pageInfo[0],
            "pageEnd" => $pageInfo[1],
            "title" => $path,
        ];

        $this->render("news_by_category", $data);
    }

    public function news(): void
    {
        $newsDetails = $this->model->getNewsDetails((int) $_GET["id"]);
        $newsDetails[0]['comments'] = $newsDetails['comments'];

        $data = [
            "title" => $newsDetails[0]["news_title"],
            "newsDetails" => $newsDetails[0],
        ];

        $this->render("news_details", $data);
    }

    public function login(): void
    {
        $csrfName = "login";

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $csrfToken = $this->csrf->generateCSRF($csrfName);
            $data = [
                "csrfName" => $csrfName,
                "csrfToken" => $csrfToken,
            ];

            $this->render("login", $data);
            exit;
        }

        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        if ($username === null || $password === null) {
            $error = "Username and password are required.";
            $csrfToken = $this->csrf->generateCSRF($csrfName);
            $data = [
                "csrfName" => $csrfName,
                "csrfToken" => $csrfToken,
                "error" => $error,
            ];

            $this->render("login", $data);
            exit;
        }

        $user = $this->model->getUserByUsername($username);

        if (!isset($_POST["csrf_name"], $_POST["csrf_token"])) {
            $error = "No CSRF token found.";
            $csrfToken = $this->csrf->generateCSRF($csrfName);
            $data = [
                "csrfName" => $csrfName,
                "csrfToken" => $csrfToken,
                "error" => $error,
            ];

            $this->render("login", $data);
            exit;
        }

        $clientCsrfName = $_POST["csrf_name"];
        $clientCsrfToken = $_POST["csrf_token"];

        if (!$this->csrf->verifyCSRF(name: $clientCsrfName, clientToken: $clientCsrfToken)) {
            $error = "Invalid CSRF token.";
            $csrfToken = $this->csrf->generateCSRF($csrfName);
            $data = [
                "csrfName" => $csrfName,
                "csrfToken" => $csrfToken,
                "error" => $error,
            ];

            $this->render("login", $data);
            exit;
        }

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
            $csrfToken = $this->csrf->generateCSRF($csrfName);
            $data = [
                "csrfName" => $csrfName,
                "csrfToken" => $csrfToken,
                "error" => $error,
            ];
            $this->render("login", $data);
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

    public function createNews(): void
    {
        $this->checkPrivilege(JOURNALIST);
        $categoryList = $this->model->getCategoryList();

        $csrfName = "createNews";
        $csrfToken = $this->csrf->generateCSRF($csrfName);

        $data = [
            "title" => "Create News",
            "categoryList" => $categoryList,
            "csrfToken" => $csrfToken,
            "csrfName" => $csrfName,
        ];
        $this->render("create_news", $data);
    }

    public function createNewsSubmit(): void
    {
        $this->checkPrivilege(JOURNALIST);

        if (!isset($_POST["csrf_name"], $_POST["csrf_token"])) {
            session_start();
            $_SESSION["newsCreateStatus"] = false;
            $_SESSION["newsCreateError"] = "No CSRF token found.";
            session_write_close();

            header("Location: /news/create");
            exit();
        }

        $csrfName = $_POST["csrf_name"];
        $csrfToken = $_POST["csrf_token"];

        if (!$this->csrf->verifyCSRF(name: $csrfName, clientToken: $csrfToken)) {
            session_start();
            $_SESSION["newsCreateStatus"] = false;
            $_SESSION["newsCreateError"] = "Invalid CSRF token.";
            session_write_close();

            header("Location: /news/create");
            exit();
        }

        if (
            !isset($_POST["news_title"]) || $_POST["news_title"] === "" ||
            !isset($_POST["news_subtitle"]) || $_POST["news_subtitle"] === "" ||
            !isset($_POST["body"]) || $_POST["body"] === "" ||
            !isset($_POST["category"]) || $_POST["category"] === []
        ) {
            session_start();
            $_SESSION["newsCreateStatus"] = false;
            session_write_close();

            header("Location: /news/create");
            exit();
        }

        $newsTitle = $_POST["news_title"];
        $newsSummary = $_POST["news_subtitle"];
        $newsBody = $_POST["body"];
        $categoryIdList = $_POST["category"];

        $addHasError = $this->model->addNewsToDB(
            newsTitle: $newsTitle,
            newsSummary: $newsSummary,
            newsBody: $newsBody,
            categoryIdList: $categoryIdList,
        );

        // success
        if (!$addHasError) {
            session_start();
            $_SESSION["newsCreateStatus"] = true;
            session_write_close();

            header("Location: /news/create");
            exit;
        }

        // fail
        session_start();
        $_SESSION["newsCreateStatus"] = false;
        $_SESSION["newsCreateError"] = $addHasError;
        session_write_close();

        header("Location: /news/create");
        error_log($addHasError);
        echo "Sorry, something went wrong. News was not created. Please try again later.";
        exit;
    }

    public function editNews(): void
    {
        $this->checkPrivilege(EDITOR);

        $newsDetails = $this->model->getNewsDetails((int) $_GET["id"]);
        $categoryList = $this->model->getCategoryList();

        $data = [
            "title" => "Edit News",
            "newsDetails" => $newsDetails,
            "categoryList" => $categoryList,
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
            !isset($_POST["body"]) || $_POST["body"] === "" ||
            !isset($_POST["category"]) || $_POST["category"] === []
        ) {
            session_start();
            $_SESSION["newsEditStatus"] = false;
            session_write_close();

            header("Location: /news/edit?id=" . $_POST["news_id"]);
            exit();
        }

        $newsId = (int) $_POST["news_id"];
        $newsTitle = $_POST["news_title"];
        $newsSummary = $_POST["news_subtitle"];
        $newsBody = $_POST["body"];
        $categoryIdList = $_POST["category"];

        $updateHasError = $this->model->updateNewsInDB(
            newsId: $newsId,
            newsTitle: $newsTitle,
            newsSummary: $newsSummary,
            newsBody: $newsBody,
            categoryIdList: $categoryIdList,
        );

        // success
        if (!$updateHasError) {
            session_start();
            $_SESSION["newsEditStatus"] = true;
            session_write_close();

            header("Location: /news/edit?id=" . $_POST["news_id"]);
            exit;
        }

        // fail
        error_log("Error updating news in DB: " . $updateHasError);
        header("HTTP/1.1 500 Internal Server Error");
        echo "Sorry, something went wrong. News was not updated. Please try again later.";
        exit;
    }

    public function deleteNews(): void
    {
        $this->checkPrivilege(EDITOR);

        $newsId = (int) $_GET["id"];
        $deleteStatus = $this->model->deleteNewsFromDB($newsId);
        if ($deleteStatus) {
            error_log("Error deleting news from DB: " . $deleteStatus);
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. News was not deleted. Please try again later.";
            exit;
        }

        header("Location: /");
        exit;
    }

    public function addComment(): void
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            session_write_close();
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                session_write_close();
                header("Location: /news?id=" . (int)$_POST['news_id']);
                exit;
            }

            $newsId = (int) $_POST['news_id'];

            $newsDetails = $this->model->getNewsDetails($newsId);
            if (!$newsDetails || !$newsDetails[0]['comments_enabled']) {
                session_write_close();
                header("Location: /news?id=" . $newsId);
                exit;
            }

            $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
            if ($comment === null || $comment === "" || strlen($comment) > 1000) {
                session_write_close();
                header("Location: /news?id=" . $newsId);
                exit;
            }

            // Reply-to-comment handling
            $parentCommentId = isset($_POST['parent_comment_id']) ? (int)$_POST['parent_comment_id'] : null;
            if ($parentCommentId && !$this->model->commentExists($parentCommentId)) {
                session_write_close();
                header("Location: /news?id=" . $newsId);
                exit;
            }

            error_log("Adding comment for news_id: $newsId, user_id: {$_SESSION['user_id']}, comment: $comment, parent_comment_id: " . ($parentCommentId ?: 'NULL'));

            $this->model->addCommentToDB($newsId, $_SESSION['user_id'], $comment, $parentCommentId);

            session_write_close();
            header("Location: /news?id=" . $newsId);
            exit;
        }

        session_write_close();
    }

    // Method to enable/disable comments
    public function enableComments(): void
    {
        $this->checkPrivilege(EDITOR);
        if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
            header('Location: /admin');
            exit;
        }

        $newsId = (int)($_GET['id'] ?? 0);
        if ($newsId > 0) {
            $this->model->toggleComments($newsId, true);
        }
        header('Location: /news?id=' . $newsId);
        exit;
    }

    public function disableComments(): void
    {
        $this->checkPrivilege(EDITOR);
        if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
            header('Location: /admin');
            exit;
        }

        $newsId = (int)($_GET['id'] ?? 0);
        if ($newsId > 0) {
            $this->model->toggleComments($newsId, false);
        }
        header('Location: /news?id=' . $newsId);
        exit;
    }

    // delete
    public function deleteComment(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $commentId = (int)($_GET['id'] ?? 0);
        $newsId = (int)($_GET['news_id'] ?? 0);

        // CSRF 
        if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
            header("Location: /news?id=" . $newsId);
            exit;
        }

        $commentorId = $this->model->getCommentorId($commentId);

        if (!$this->model->commentExists($commentId)) {
            header("Location: /news?id=" . $newsId);
            exit;
        }

        if ($_SESSION['user_id'] != $commentorId && $_SESSION['privilege'] < EDITOR) {
            header("Location: /news?id=" . $newsId);
            exit;
        }

        $this->model->deleteComment($commentId);
        header("Location: /news?id=" . $newsId);
        exit;
    }

    // edit
    public function editComment(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $commentId = (int)($_POST['comment_id'] ?? 0);
        $newsId = (int)($_POST['news_id'] ?? 0);
        $newComment = filter_input(INPUT_POST, 'new_comment', FILTER_SANITIZE_STRING);


        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            header("Location: /news?id=" . $newsId);
            exit;
        }

        if ($newComment === null || $newComment === "" || strlen($newComment) > 1000) {
            header("Location: /news?id=" . $newsId);
            exit;
        }

        $comments = $this->model->getCommentsForNews($newsId);
        $commentExists = false;
        foreach ($comments as $comment) {
            if ($comment['comment_id'] == $commentId) {
                $commentExists = true;
                $commentorId = $comment['commentor'];
                break;
            }
        }

        if (!$commentExists) {
            header("Location: /news?id=" . $newsId);
            exit;
        }

        if ($_SESSION['user_id'] != $commentorId && $_SESSION['privilege'] < EDITOR) {
            header("Location: /news?id=" . $newsId);
            exit;
        }

        $this->model->updateComment($commentId, $newComment);
        header("Location: /news?id=" . $newsId);
        exit;
    }

    public function admin(): void
    {
        $this->currNewsList = $this->paginator->start(null);
        $totalPages = $this->paginator->getTotalPages();
        $pageInfo = $this->paginator->getPageRange();

        if (isset($_GET["page"]) || isset($_GET["display"])) {
            if (isset($_GET["display"])) {
                $this->paginator->changeAmountToDisplay((int) $_GET["display"]);
                $totalPages = $this->paginator->getTotalPages();

                if ($this->paginator->currentPage > $totalPages) {
                    $this->paginator->currentPage = $totalPages;
                }
            }

            $page = $this->paginator->currentPage;
            if (isset($_GET["page"]) && $_GET["page"] <= $totalPages && $_GET["page"] >= 0) {
                $page = (int) $_GET["page"];
            }

            $this->currNewsList = $this->paginator->skipToPage($page, null);
        }

        $data = [
            "newsList" => $this->currNewsList,
            "totalPages" => $totalPages,
            "currentPage" => $this->paginator->currentPage,
            "pageStart" => $pageInfo[0],
            "pageEnd" => $pageInfo[1],
        ];

        $this->render("admin", $data);
    }

    public function pageNotFound(): void
    {
        $this->render("404", []);
    }
}
