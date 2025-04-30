<?php

class Model
{
    public PDO $db;

    public function __construct()
    {
        $dbPath = MODEL . "db.sqlite";
        try {
            $this->db = new PDO("sqlite:$dbPath");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $err) {
            error_log("Database connection failed: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    private function getAuthorName(int $authorId): string | null
    {
        $statement = $this->db->prepare("
            SELECT user_name FROM user
            WHERE user_id = :authorId 
        ");

        $statement->execute(["authorId" => $authorId]);
        $result = $statement->fetch();
        return $result ? $result["user_name"] : null;
    }

    public function getAllNews(): array
    {
        $statement = $this->db->query("
            SELECT * FROM news
            ORDER BY edited_date
        ");
        $newsList = $statement->fetchAll(PDO::FETCH_ASSOC);


        foreach ($newsList as $key => $val) {
            $newsList[$key]["author"] = $this->getAuthorName($val["author_id"]);
        }

        return $newsList;
    }

    public function getUserByUsername(string $username): ?array
    {
        $statement = $this->db->prepare("SELECT * FROM user WHERE user_name = :username");
        $statement->execute(["username" => $username]);
        return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getNewsDetails(int $id): ?array
    {
        $statement = $this->db->prepare("
            SELECT * FROM news 
            WHERE news_id = :news_id
        ");
        $statement->execute(["news_id" => $id]);
        $newsDetails = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$newsDetails) {
            return null;
        }

        $newsDetails["author"] = $this->getAuthorName($newsDetails["author_id"]);
        return $newsDetails;
    }

    public function addNewsToDB(): void
    {
        try {
            session_start();
            $authorId = $_SESSION['user_id'];
            session_write_close();

            $statement = $this->db->prepare("
            ");
            $statement->execute([]);
        } catch (PDOException $err) {
            error_log("Error adding news to DB: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. News was not created. Please try again later.";
            exit();
        }
    }

    public function updateNewsInDB(int $newsId): void
    {
        try {
            session_start();
            $authorId = $_SESSION['user_id'];
            session_write_close();

            $statement = $this->db->prepare("
                INSERT INTO news (news_title, news_summary, body, author_id, created_date, edited_date)
                VALUES (:title, :summary, :body, :authorId, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
            ");
            
            $statement->execute([
                "title" => $_POST["news_title"],
                "summary" => $_POST["news_summary"],
                "body" => $_POST["body"],
                "newsId" => $newsId,
            ]);
        } catch (PDOException $err) {
            error_log("Error updating news in DB: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. News was not updated. Please try again later.";
            exit();
        }
    }

    public function deleteNewsFromDB(int $newsId): void
    {
        try {
            $statement = $this->db->prepare("DELETE FROM news WHERE news_id = :newsId");
            $statement->execute(["newsId" => $newsId]);
        } catch (PDOException $err) {
            error_log("Error deleting news from DB: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. News was not deleted. Please try again later.";
            exit();
        }
    }

    public function getCommentsForNews(int $newsId): array
    {
        $statement = $this->db->prepare("
            SELECT c.*, u.user_name AS commentor_name 
            FROM comment c 
            LEFT JOIN user u ON c.commentor = u.user_id 
            WHERE c.news_id = :newsId
        ");
        $statement->execute(["newsId" => $newsId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addCommentToDB(int $newsId, int $commentorId, string $comment): void
    {
        $statement = $this->db->prepare("
            INSERT INTO comment (comment, commentor, news_id)
            VALUES (:comment, :commentor, :newsId)
        ");
        $statement->execute([
            "comment" => $comment,
            "commentor" => $commentorId,
            "newsId" => $newsId,
        ]);
    }
}
