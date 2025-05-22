<?php

class Model
{
    private PDO $db;

    public function __construct()
    {
        $dbPath = MODEL . "db.sqlite";
        try {
            $this->db = new PDO("sqlite:$dbPath");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->exec('PRAGMA foreign_keys = ON');
        } catch (PDOException $err) {
            error_log("Database connection failed: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function getTotalNewsCount(?string $category): int
    {
        try {
            if (!$category) {
                $statement = $this->db->query("SELECT COUNT(*) FROM news");
                return $statement->fetch(PDO::FETCH_ASSOC)["COUNT(*)"];
            }

            $statement = $this->db->prepare("
                SELECT COUNT(*)
                FROM news n
                JOIN news_category nc ON nc.news_id = n.news_id
                JOIN category c ON nc.category_id = c.category_id
                JOIN user u ON n.author_id = u.user_id
                WHERE c.category = :category
            ");
            $statement->execute(["category" => $category]);
            return $statement->fetch(PDO::FETCH_ASSOC)["COUNT(*)"];
        } catch (PDOException $err) {
            error_log("Error getting total news count: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    private function getNewsCategory(array &$newsList): void
    {
        try {
            for ($i = 0; $i < count($newsList); $i++) {
                $statement = $this->db->prepare("
                    SELECT c.category
                    FROM category c
                    JOIN news_category nc ON nc.category_id = c.category_id
                    WHERE nc.news_id = :newsId
                ");
                $statement->execute(["newsId" => $newsList[$i]["news_id"]]);
                $categoryList = $statement->fetchAll(PDO::FETCH_ASSOC);

                $categories = [];
                for ($j = 0; $j < count($categoryList); $j++) {
                    array_push($categories, $categoryList[$j]["category"]);
                }
                $newsList[$i]["category"] = $categories;
            }
        } catch (PDOException $err) {
            error_log("Error getting total news count: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function getNewsList(int $start, int $end): array
    {
        try {
            $statement = $this->db->prepare("
                SELECT news.*,user.user_name AS author
                FROM news
                JOIN user ON news.author_id = user.user_id
                ORDER BY edited_date DESC
                LIMIT :end OFFSET :start
            ");
            $statement->execute(["start" => $start, "end" => $end]);
            $newsList = $statement->fetchAll(PDO::FETCH_ASSOC);

            $this->getNewsCategory($newsList);

            return $newsList;
        } catch (PDOException $err) {
            error_log("Error getting news list: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function getNewsListByCategory(int $start, int $end, string $category): array
    {
        try {
            $statement = $this->db->prepare("
                SELECT n.*,u.user_name AS author
                FROM news n
                JOIN news_category nc ON nc.news_id = n.news_id
                JOIN category c ON nc.category_id = c.category_id
                JOIN user u ON n.author_id = u.user_id
                WHERE c.category = :category
                ORDER BY edited_date DESC
                LIMIT :end OFFSET :start
            ");
            $statement->execute(["start" => $start, "end" => $end, "category" => $category]);
            $newsList = $statement->fetchAll(PDO::FETCH_ASSOC);

            $this->getNewsCategory($newsList);

            return $newsList;
        } catch (PDOException $err) {
            error_log("Error getting news list by category: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function getLatestNews(): ?array
    {
        try {
            $statement = $this->db->query("
                SELECT *,u.user_name as author
                FROM news n
                JOIN user u ON u.user_id = n.author_id
                ORDER BY edited_date DESC 
                LIMIT 1
            ");
            $latestNews = [$statement->fetch(PDO::FETCH_ASSOC)];

            if (!$latestNews[0]) {
                return null;
            }

            $this->getNewsCategory($latestNews);
            return $latestNews;
        } catch (PDOException $err) {
            error_log("Error getting latest news: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function getUserByUsername(string $username): ?array
    {
        try {
            $statement = $this->db->prepare("SELECT * FROM user WHERE user_name = :username");
            $statement->execute(["username" => $username]);
            return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $err) {
            error_log("Error getting user by username: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function getNewsDetails(int $id): ?array
    {
        try {
            $statement = $this->db->prepare("
                SELECT news.*,user.user_name AS author
                FROM news 
                JOIN user ON news.author_id = user.user_id
                WHERE news_id = :news_id
            ");
            $statement->execute(["news_id" => $id]);
            $newsDetails = [$statement->fetch(PDO::FETCH_ASSOC)];

            $this->getNewsCategory($newsDetails);

            if (!$newsDetails[0]) {
                return null;
            }

            $newsDetails['comments'] = $this->getCommentsForNews($id);
            return $newsDetails;
        } catch (PDOException $err) {
            error_log("Error getting news details: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function addNewsToDB(
        string $newsTitle,
        string $newsSummary,
        string $newsBody,
        array $categoryIdList
    ): void {
        try {
            session_start();
            $authorId = $_SESSION['user_id'];
            session_write_close();

            $this->db->beginTransaction();

            $statement1 = $this->db->prepare("
                INSERT INTO news 
                    (news_title, news_subtitle, body, author_id) 
                VALUES 
                    (:title, :summary, :body, :authorId)
            ");
            $statement1->execute([
                "title" => $newsTitle,
                "summary" => $newsSummary,
                "body" => $newsBody,
                "authorId" => $authorId,
            ]);

            $statement2 = $this->db->prepare("SELECT news_id FROM news WHERE news_title = :title");
            $statement2->execute(["title" => $newsTitle]);
            $news = $statement2->fetch(PDO::FETCH_ASSOC);

            foreach ($categoryIdList as $categoryId) {
                $statement3 = $this->db->prepare("
                    INSERT INTO news_category (news_id, category_id) VALUES (:newsId, :categoryId)
                ");
                $statement3->execute(["newsId" => $news["news_id"], "categoryId" => $categoryId]);
            }

            $this->db->commit();
        } catch (PDOException $err) {
            error_log("Error adding news to DB: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. News was not created. Please try again later.";
            exit();
        }
    }

    public function updateNewsInDB(
        int $newsId,
        string $newsTitle,
        string $newsSummary,
        string $newsBody,
        array $categoryIdList
    ): void {
        try {
            $this->db->beginTransaction();

            $statement1 = $this->db->prepare("
                UPDATE news 
                SET news_title = :title, news_subtitle = :summary, body = :body, edited_date = CURRENT_TIMESTAMP
                WHERE news_id = :newsId
            ");
            $statement1->execute([
                "newsId" => $newsId,
                "title" => $newsTitle,
                "summary" => $newsSummary,
                "body" => $newsBody,
            ]);

            $statement2 = $this->db->prepare("DELETE FROM news_category WHERE news_id = :newsId");
            $statement2->execute(["newsId" => $newsId]);

            foreach ($categoryIdList as $category) {
                $statement3 = $this->db->prepare("
                    INSERT INTO news_category (news_id, category_id) VALUES (:newsId, :categoryId)
                ");
                $statement3->execute(["newsId" => $newsId, "categoryId" => $category]);
            }

            if (!$this->db->commit()) {
                throw new Exception("Transaction failed while editing {$_POST["news_id"]}");
            }
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
        try {
            $statement = $this->db->prepare("
                SELECT c.*, u.user_name AS commentor_name 
                FROM comment c 
                LEFT JOIN user u ON c.commentor = u.user_id 
                WHERE c.news_id = :newsId
                ORDER BY c.created_date ASC 
            ");
            $statement->execute(["newsId" => $newsId]);
            $comments = $statement->fetchAll(PDO::FETCH_ASSOC);

            //reply of a comment
            $commentTree = [];
            $commentMap = [];
            foreach ($comments as $comment) {
                $comment['replies'] = [];
                $commentMap[$comment['comment_id']] = $comment;
            }
            foreach ($commentMap as $commentId => &$comment) {
                if ($comment['parent_comment_id']) {
                    $commentMap[$comment['parent_comment_id']]['replies'][] = &$comment;
                } else {
                    $commentTree[] = &$comment;
                }
            }
            return $commentTree;
        } catch (PDOException $err) {
            error_log("Error getting comments for news: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function addCommentToDB(int $newsId, int $commentorId, string $comment, ?int $parentCommentId = null): void
    {
        try {
            error_log("Adding comment to DB: news_id: $newsId, commentor_id: $commentorId, comment: $comment, parent_comment_id: " . ($parentCommentId ?: 'NULL'));

            $statement = $this->db->prepare("
                INSERT INTO comment (comment, commentor, news_id, parent_comment_id)
                VALUES (:comment, :commentor, :newsId, :parentCommentId)
            ");
            $statement->execute([
                "comment" => $comment,
                "commentor" => $commentorId,
                "newsId" => $newsId,
                "parentCommentId" => $parentCommentId,
            ]);
        } catch (PDOException $err) {
            error_log("Error adding comment to DB: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function toggleComments(int $newsId, bool $enable): void
    {
        try {
            $statement = $this->db->prepare("
                UPDATE news 
                SET comments_enabled = :enabled
                WHERE news_id = :newsId
            ");
            $statement->execute([
                'enabled' => $enable ? 1 : 0,
                'newsId' => $newsId,
            ]);
        } catch (PDOException $err) {
            error_log("Error toggling comments: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function getCommentorId(int $commentId): int
    {
        try {
            $statement = $this->db->prepare("
                SELECT commentor AS commentorId
                FROM comment
                WHERE comment_id = :commentId
            ");
            $statement->execute(["commentId" => $commentId]);
            return $statement->fetch(PDO::FETCH_ASSOC)["commentorId"];
        } catch (PDOException $err) {
            error_log("Error getting commentor ID: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function commentExists(int $commentId): bool
    {
        try {
            $statement = $this->db->prepare("SELECT COUNT(*) FROM comment WHERE comment_id = :commentId");
            $statement->execute(['commentId' => $commentId]);
            return $statement->fetchColumn() > 0;
        } catch (PDOException $err) {
            error_log("Error checking comment existence: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    public function deleteComment(int $commentId): void
    {
        try {
            $statement = $this->db->prepare("DELETE FROM comment WHERE comment_id = :commentId");
            $statement->execute(["commentId" => $commentId]);
        } catch (PDOException $err) {
            error_log("Error deleting comment: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }

    // edit 
    public function updateComment(int $commentId, string $newComment): void
    {
        try {
            $statement = $this->db->prepare("
                UPDATE comment 
                SET comment = :newComment, created_date = CURRENT_TIMESTAMP
                WHERE comment_id = :commentId
            ");
            $statement->execute([
                "newComment" => $newComment,
                "commentId" => $commentId,
            ]);
        } catch (PDOException $err) {
            error_log("Error updating comment: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }
    public function getCategoryList(): array
    {
        try {
            $statement = $this->db->query("SELECT * FROM category");
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $err) {
            error_log("Error getting category list: " . $err->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            echo "Sorry, something went wrong. Please try again later.";
            exit();
        }
    }
}
