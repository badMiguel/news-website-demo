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

    public function getTotalNewsCount(): int
    {
        $statement = $this->db->query("SELECT COUNT(*) FROM news");
        return $statement->fetch(PDO::FETCH_ASSOC)["COUNT(*)"];
    }

    public function getNewsList(int $start, int $end): array
    {
        $statement = $this->db->prepare("
            SELECT news.*,user.user_name AS author
            FROM news
            JOIN user ON news.author_id = user.user_id
            ORDER BY edited_date
            LIMIT :end OFFSET :start
        ");
        $statement->execute(["start" => $start, "end" => $end]);
        $newsList = $statement->fetchAll(PDO::FETCH_ASSOC);

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
            SELECT news.*,user.user_name AS author
            FROM news 
            JOIN user ON news.author_id = user.user_id
            WHERE news_id = :news_id
        ");
        $statement->execute(["news_id" => $id]);
        $newsDetails = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$newsDetails) {
            return null;
        }

        return $newsDetails;
    }

    public function addNewsToDB(string $newsTitle, string $newsSummary, string $newsBody): void
    {
        try {
            session_start();
            $authorId = $_SESSION['user_id'];
            session_write_close();

            $statement = $this->db->prepare("
                INSERT INTO news 
                    (news_title, news_subtitle, body, author_id) 
                VALUES 
                    (:title, :summary, :body, :authorId)
            ");
            $statement->execute([
                "title" => $newsTitle,
                "summary" => $newsSummary,
                "body" => $newsBody,
                "authorId" => $authorId,
            ]);
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
            $statement = $this->db->prepare("
                UPDATE news 
                SET news_title = :title, news_subtitle = :summary, body = :body, edited_date = CURRENT_TIMESTAMP
                WHERE news_id = :newsId
            ");
            $statement->execute([
                "title" => $_POST["news_title"],
                "summary" => $_POST["news_subtitle"],
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
