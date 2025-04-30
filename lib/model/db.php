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
}
